<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\TaskModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Dashboard
{
    private $container;
    private $auth;
    private $user;
    private $taskModel;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->taskModel            = new TaskModel($this->container->get('db'));
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin', 'admin', 'staff');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }
    }

    public function statistic(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];

        $userId = !in_array($this->user->role, ['superadmin', 'admin']) ? $this->user->id : null;

        $statistic = $this->taskModel->statistic($userId);

        if (!empty($statistic)) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => [
                'statistic' => $statistic,
                'statistic_by_month' => $this->taskModel->statisticByMonth($userId)
            ]];
        }

        return JsonResponse::withJson($response, $result, 200);
    }
}
