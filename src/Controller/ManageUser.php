<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\UserModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ManageUser
{
    private $container;
    private $auth;
    private $user;
    private $userModel;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->userModel            = new UserModel($this->container->get('db'));
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin', 'admin');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }
    }

    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $list   = $this->userModel->list($params);

        if (!empty($list['data'])) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list['data']];
        }

        $result['pagination'] = [
            'page' => (int) $params['page'],
            'prev' => $params['page'] > 1,
            'next' => ($list['total'] - ($params['page'] * $params['limit'])) > 0,
            'total' => $list['total']
        ];
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function detail(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        
        $data   = $this->userModel->detail($params['id']);
        if (!empty($data)) {
            $result = ['status' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function save(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();

        $post['role']           = 'staff';
        $result                 = $this->userModel->save($post);

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, $parameters): Response
    {
        $result = $this->userModel->delete($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }

    public function bulkDelete(Request $request, Response $response): Response
    {
        $result = $this->userModel->deleteBatch($request->getParsedBody());
        return JsonResponse::withJson($response, $result, 200);
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $post                       = $request->getParsedBody();
        $id                         = isset($post["id"]) ? $post["id"] : '';
        $password                   = isset($post["password"]) ? $post["password"] : '';
        $confirmPassword            = isset($post["confirmPassword"]) ? $post["confirmPassword"] : '';

        $result['status']   = false;
        $result['message'] = 'Gagal mengubah password';

        if ($password == $confirmPassword) {
            $process            = $this->userModel->updatePassword($id, $password);

            if ($process) {
                $result['status']   = true;
                $result['message'] = 'Password berhasil diubah';
            }
        }

        return JsonResponse::withJson($response, $result, 200);
    }
}
