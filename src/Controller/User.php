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

        $roles                      = array('superadmin','admin','staff','customer');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }

        $this->userModel            = new UserModel($this->container->get('db'));
    }

    public function info(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['data']     = $this->user;

        return JsonResponse::withJson($response, $result, 200);
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $post                       = $request->getParsedBody();
        $new_password               = isset($post["new_password"]) ? $post["new_password"] : '';
        $confirm_password           = isset($post["confirm_password"]) ? $post["confirm_password"] : '';

        $result['status']   = false;

        if ($new_password == $confirm_password) {
            $result['status']   = true;
            $result['data']     = $this->userModel->updatePassword($this->user->id, $new_password);
        }


        return JsonResponse::withJson($response, $result, 200);
    }
}
