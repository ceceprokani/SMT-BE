<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\MasterDataModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MasterData
{
    private $container;
    private $auth;
    private $user;
    private $model;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->model                = new MasterDataModel($this->container->get('db'));
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin', 'admin');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }
    }

    public function listJabatan(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $params['role'] = 'staff';
        $list   = $this->model->listJabatan($params);

        if (!empty($list)) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function listUser(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $params['role'] = 'staff';
        $list   = $this->model->listUser($params);

        if (!empty($list)) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }
}
