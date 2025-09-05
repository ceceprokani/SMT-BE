<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\UserModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class User
{
    private $container;
    private $auth;
    private $user;
    private $userModel;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin','admin','staff');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }

        $this->userModel            = new UserModel($this->container);
    }

    public function info(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['data']     = $this->user;

        $detailUser = $this->userModel->detail($this->user->id);
        if ($detailUser) {
            $result['data'] = array_merge((array)$result['data'], [
                'name' => $detailUser->nama,
                'email' => $detailUser->email,
                'phone' => $detailUser->telepon,
                'address' => $detailUser->alamat,
                'has_password_updated' => $this->user->role == 'superadmin' ? '1' : $detailUser->has_password_updated
            ]);
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $post                       = $request->getParsedBody();
        $new_password               = isset($post["new_password"]) ? $post["new_password"] : '';
        $confirm_password           = isset($post["confirm_password"]) ? $post["confirm_password"] : '';

        if ($new_password == $confirm_password) {
            $result     = $this->userModel->updatePassword($this->user->id, $new_password, 1);
        }


        return JsonResponse::withJson($response, $result, 200);
    }

    public function updateProfile(Request $request, Response $response): Response
    {
        $post                       = $request->getParsedBody();
        $name                       = isset($post["name"]) ? $post["name"] : '';
        $phone                      = isset($post["phone"]) ? $post["phone"] : '';
        $address                    = isset($post["address"]) ? $post["address"] : '';

        $result = $this->userModel->updateProfile($this->user->id, $name, $phone, $address);

        return JsonResponse::withJson($response, $result, 200);
    }
}
