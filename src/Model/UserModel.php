<?php

declare(strict_types=1);

namespace App\Model;

use Exception;
use Pimple\Psr11\Container;

use App\Helper\General;

final class UserModel
{
    protected $database;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(Container $container)
    {
        $this->database         = $container->get('db');
        $this->general          = new General($container);
    }

    public function countUser()
    {
        return $this->db()->table('users')->count();
    }

    public function buildQueryList($params=null) {
        $getQuery = $this->db()->table('users');
        $getQuery->select($getQuery->raw('users.id, users.nama, users.email, users.telepon, users.status, jabatan.nama as jabatan'));
        $getQuery->where('users.role', '!=', 'superadmin');
        $getQuery->leftJoin('jabatan', 'jabatan.id', '=', 'users.jabatan_id');
        
        if(!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where('users.nama', 'LIKE', "%$keywords%");
        }

        return $getQuery;
    }

    public function list($params)
    {
        $getQuery = $this->buildQueryList($params);
        
        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $getQuery->orderBy("nama", "asc"); 
        
        $list = $getQuery->get();

        return ['data' => $list, 'total' => $totalData];
    }

    public function detail($id, $withPassword = false) {
        $result = $this->db()->table('users')
                    ->select($this->db()->raw('id, nama, email, telepon, jabatan_id, alamat, has_password_updated, status'.($withPassword ? ', password, password_raw' : '')))
                    ->where('id', $id)
                    ->first();

        return $result;
    }

    public function save($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $checkUser = $this->db()->table('users')->where('username', $params['email'])->where('id', '!=', $params['id'])->first();
        
        if (!empty($checkUser)) {
            $result['message'] = 'Email sudah terdaftar disistem! Silahkan gunakan email lain';
        } else {
            // register user auth
            $dataUser = [
                'username' => $params['email'],
                'role' => $params['role'],
                'nama' => $params['nama'],
                'email' => $params['email'],
                'telepon' => $params['telepon'],
                'jabatan_id' => $params['jabatan_id'],
                'alamat' => $params['alamat'],
                'status' => isset($params['status']) ? $params['status'] : 'active',
            ];

            if (!empty($params['password'])) {
                $dataUser = array_merge([
                    'password' => password_hash($params['password'], PASSWORD_BCRYPT),
                    'password_raw' => $params['password'],
                ], $dataUser);
            }

            if (empty($params['id'])) {
                $dataUser = array_merge(['created_at' => date('Y-m-d H:i:s')], $dataUser);
                $process = $this->db()->table('users')->insert($dataUser);

                if ($process) {
                    // mengirim pesan slack informasi akun login
                    $bodyMessage =  "👋 *Halo " . $dataUser['nama'] . "*\n" .
                                    "*Berikut adalah informasi akun Anda:*\n\n" .
                                    "*Username:* " . $params['email'] . "\n" .
                                    "*Password:* " . $params['password'] . "\n\n" .
                                    "*Demi keamanan, harap segera login dan mengganti password Anda melalui halaman profil. Jangan membagikan password ini kepada siapapun*\n" .
                                    "*Terima kasih*\n";
                    $this->general->sendMessagePrivate($params['email'], $bodyMessage);
                }
            } else {
                $process = $params['id'];
                $this->db()->table('users')->where('id', $params['id'])->update($dataUser);
            }

            if (!empty($process)) {
                $result                 = ['status' => true, 'message' => 'Data berhasil disimpan'];
            }
        }

        return $result;
    }

    public function delete($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('users')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->db()->table('users')->where('id', $id)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('users')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $process = $this->db()->table('users')->whereIn('id', $listId)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function updateProfile($id, $name, $phone, $address) {
        $data = [
                'nama' => $name,
                'telepon' => $phone,
                'alamat' => $address,
        ];
        $result = ['status' => false, 'message' => 'Gagal memperbarui profil.'];
        $process = $this->db()->table('users')
            ->where('id', $id)
            ->update($data);
        if ($process) {
            $result = ['status' => true, 'message' => 'Profil berhasil diperbarui.', 'data' => [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
            ]];
        }
        return $result;
    }

    public function updatePassword($id, $password, $hasPasswordUpdatedByUser = 0) {
        $data = [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'password_raw' => $password,
            'has_password_updated' => $hasPasswordUpdatedByUser
        ];
        $result = ['status' => false, 'message' => 'Gagal memperbarui password.'];
        $detailUser = $this->db()->table('users')->where('id', $id)->first();
        $process = $this->db()->table('users')
            ->where('id', $id)
            ->update($data);
        if ($process) {
            // mengirim pesan slack informasi akun login
            $bodyMessage =  "👋 *Halo " . $detailUser->nama . "*\n" .
                            "*Berikut adalah informasi akun Anda:*\n\n" .
                            "*Username:* " . $detailUser->email . "\n" .
                            "*Password:* " . $password . "\n\n" .
                            "*Demi keamanan, harap segera login dan mengganti password Anda melalui halaman profil. Jangan membagikan password ini kepada siapapun*\n" .
                            "*Terima kasih*\n";
            $this->general->sendMessagePrivate($detailUser->email, $bodyMessage);
            $result = ['status' => true, 'message' => 'Password berhasil diperbarui.'];
        }
        return $result;
    }
}