<?php

declare(strict_types=1);

namespace App\Model;

use Exception;
use stdClass;

use DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\UnencryptedToken;

final class AuthModel
{
    protected $database;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(\Pecee\Pixie\Connection $database)
    {
        $this->database       = $database;
    }

    public function getDeviceInfo()
    {
        $devicedata = array();
        $devicedata['time'] = date('Y-m-d H:i:s');

        $devicedata['ip_address']   = $this->getUserIP();

        $device_status              = "Unknown";
        $devicedata['device']       = $device_status;
        $devicedata['platform']     = "Unknown";
        $devicedata['browser']      = "Unknown";
        $devicedata['version']      = "Unknown";

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $agent = new \Jenssegers\Agent\Agent();
            $agent->setUserAgent($_SERVER['HTTP_USER_AGENT']);
            if ($agent->isMobile()) {
                $device_status = "Mobile";
            } elseif ($agent->isTablet()) {
                $device_status = "Tablet";
            } elseif ($agent->isDesktop()) {
                $device_status = "Desktop";
            } elseif ($agent->isRobot()) {
                $device_status = "Robot";
            }

            $devicedata['device']       = $device_status;
            $devicedata['platform']     = $agent->platform();
            $devicedata['browser']      = $agent->browser();
            $devicedata['version']      = $agent->version($devicedata['browser']);
        }

        return $devicedata;
    }

    public function getUserIP()
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
            $proxy_list = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            $client_ip  = trim(end($proxy_list));
            if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
                $ip_address = $client_ip;

                if (array_key_exists("HTTP_CF_CONNECTING_IP", $_SERVER)) {
                    $ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];
                }
            }
        }

        return $ip_address;
    }

    public function checkUsers($username)
    {
        return $this->db()->table('users')
            ->where('username', $username)
            ->first();
    }

    public function processLogin($username = "", $password = "")
    {
        $isAllow     = false;
        $updatehash  = false;
        $loginstatus = false;
        $message     = "";
        $loginmethod = 'normal';

        if(empty($username) || empty($password)) {
            $message = "Username atau Password tidak boleh kosong.";
        } else {
            $getData = $this->db()->table('users');
            $getData->where('status', '=', 'active');
            $getData->where(function ($relation) use ($username) {
                $relation->orWhere($relation->raw('lower(username)'), '=', strtolower($username));
            });

            $getUser    = $getData->first();

            if(empty($getUser->id)) {
                $message    = "User tidak ditemukan.";
            } else {
                $isAllow    = true;
            }

            if($isAllow) {
                // check if using newer hash (general user)
                if (password_verify($password, $getUser->password)) {
                    $loginstatus = true;
                }

                // check if using cleartext (general user)
                if ($password == $getUser->password) {
                    $loginstatus = true;
                    $updatehash  = true;
                }

                if ($loginstatus) {
                    // when needed, update the hash to more stronger crypto.
                    if ($updatehash) {
                        $update_data['password'] = password_hash($password, PASSWORD_ARGON2ID);
                        $this->db()->table('users')->where('id', $getUser->id)->update($update_data);
                    }
                    
                    $result['role']         = $getUser->role;
                    $result['token']        = $this->generateToken($getUser->id, $getUser->username, $getUser->nama, $getUser->role);

                    $message                = "Login berhasil.";
                    $this->recordLoginAttempt($getUser->id, $loginmethod, "success", "signin");
                } else {
                    $message = "Password salah.";
                    $this->recordLoginAttempt($getUser->id, $loginmethod, "wrong_password", "signin");
                }
            }
        }

        $result['status']   = $loginstatus;
        $result['message']  = $message;

        return $result;
    }

    public function generateToken($id = "", $username = "", $name = "", $role = "")
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText($_ENV['APP_KEY'] ?: $_SERVER['APP_KEY']);

        $now   = new DateTimeImmutable();
        $token = $tokenBuilder
            ->issuedBy($_ENV['APP_BASE_URL'] ?: $_SERVER['APP_BASE_URL'])
            ->permittedFor($_ENV['APP_BASE_URL'] ?: $_SERVER['APP_BASE_URL'])
            ->identifiedBy('dN7mVVQC3jua')
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+7 day'))
            ->withClaim('id', $id)
            ->withClaim('username', $username)
            ->withClaim('name', $name)
            ->withClaim('role', $role)
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }

    public function recordLoginAttempt($user_id, $method, $status, $type = null, $device = null, $data = null)
    {
        $result      = false;
        $device_info = $this->getDeviceInfo();

        $insertdata['ip_address'] = $device_info['ip_address'];
        $insertdata['time']       = $device_info['time'];

        $insertdata['type']     = $type;
        $insertdata['users_id'] = $user_id;
        $insertdata['status']   = $status;
        $insertdata['method']   = $method;

        if ($device == 'mobile') {
            $insertdata['device'] = "Apps";
            if (!empty($data)) {
                $insertdata['platform'] = $data['platform'];
                $insertdata['notes']    = json_encode(array('device_id' => $data['device_id'], 'device_name' => $data['device_name']));
            } else {
                $insertdata['platform'] = $device_info['platform'];
            }
        } else {
            $insertdata['device']   = $device_info['device'];
            $insertdata['platform'] = $device_info['platform'];
            $insertdata['browser']  = $device_info['browser'];
            $insertdata['version']  = $device_info['version'];
        }

        if (!empty($data) && !is_array($data)) {
            $insertdata['notes'] = $data;
        }

        $result = $this->db()->table('authentication_log')->insert($insertdata);

        return $result;
    }

    public function validateToken($robot_invalidation = false, $allowed_type = array())
    {
        $robot_invalidation     = true;

        $result['status']       = false;
        $result['message']      = '';
        $result['data']         = new stdClass();

        $server_name = $_SERVER['SERVER_NAME'];
        if($server_name != 'localhost') {
            $server_name = 'https://' . $server_name . '/';
        }

        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $device_data = $this->getDeviceInfo();

            $token                  = $_SERVER['HTTP_AUTHORIZATION'];
            try {
                if (!$robot_invalidation) {
                    if ($device_data['device'] == 'Robot') {
                        $result['message'] = "Unauthorized Access.";

                        header('Content-Type: application/json');
                        echo json_encode($result);
                        exit();
                    }
                }

                $parser = new Parser(new JoseEncoder());
                $token = $parser->parse($token);

                $validator = new Validator();

                try {
                    $signingKey   = InMemory::plainText($_ENV['APP_KEY'] ?: $_SERVER['APP_KEY']);
                    $validator->assert($token, new IdentifiedBy('dN7mVVQC3jua'));
                    $validator->assert($token, new IssuedBy($_ENV['APP_BASE_URL'] ?: $_SERVER['APP_BASE_URL']));
                    $validator->assert($token, new PermittedFor($_ENV['APP_BASE_URL'] ?: $_SERVER['APP_BASE_URL']));
                    $validator->assert($token, new SignedWith(new Sha256(), $signingKey));
                    $validator->assert($token, new StrictValidAt(
                        new FrozenClock(new \DateTimeImmutable())
                    ));

                    assert($token instanceof UnencryptedToken);
                    $parsed_data = $token->claims()->all();

                    if (!empty($parsed_data['id'])) {
                        if (count($allowed_type) > 0) {
                            if (!in_array($parsed_data['type'], $allowed_type)) {
                                $result['message'] = "Unauthorized Access.";
                            } else {
                                $result['status']               = true;

                                $result['data']->id             = $parsed_data['id'];
                                $result['data']->username       = $parsed_data['username'];
                                $result['data']->name           = $parsed_data['name'];
                                $result['data']->role           = $parsed_data['role'];
                                $result['data']->logged_in      = true;
                            }
                        } else {
                            $result['status']               = true;

                            $result['data']->id             = $parsed_data['id'];
                            $result['data']->username       = $parsed_data['username'];
                            $result['data']->name           = $parsed_data['name'];
                            $result['data']->role           = $parsed_data['role'];
                            $result['data']->logged_in      = true;
                        }
                    } else {
                        $result['message'] = "Invalid token identifier, please recreate token.";
                    }
                } catch (RequiredConstraintsViolated $e) {
                    $result['message'] = "Authorization token is expired or invalid.";
                }
            } catch (Exception $e) {
                $result['message'] = "Token or authorization couldn't be processed.";
            }
        } else {
            $result['message'] = "Token or authorization incomplete.";
        }

        if ($result['status']) {
            return $result['data'];
        } else {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function denyAccess()
    {
        $result['status']   = false;
        $result['message']  = "Access Denied";

        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
}
