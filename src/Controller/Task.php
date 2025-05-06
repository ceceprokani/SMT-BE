<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\TaskModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Task
{
    private $container;
    private $auth;
    private $user;
    private $model;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->model                = new TaskModel($this->container->get('db'));
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin', 'admin', 'staff');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }
    }

    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];

        $params['user_id'] = $this->user->id;

        $list   = $this->model->list($params);

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
        
        $data   = $this->model->detail($params['id']);
        if (!empty($data)) {
            $result = ['status' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function save(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();

        $post['user_id']        = $this->user->id;
        $result                 = $this->model->save($post);

        return JsonResponse::withJson($response, $result, 200);
    }

    public function updateStatus(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        $result = $this->model->updateStatus($body['id'], $body['status']);
        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, $parameters): Response
    {
        $result = $this->model->delete($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }

    public function bulkDelete(Request $request, Response $response): Response
    {
        $result = $this->model->deleteBatch($request->getParsedBody());
        return JsonResponse::withJson($response, $result, 200);
    }
}
