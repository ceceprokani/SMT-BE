<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use App\Helper\TwigResponse;
use Pimple\Psr11\Container;

use App\Model\UserModel;
use App\Helper\General;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Hello
{
    private $container, $userModel, $general;
    
    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->userModel    = new UserModel($this->container->get('db'));
        $this->general      = new General($this->container);
    }

    public function getStatusAPI(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['message']  = "Tugasin API Backend";
        $result['version']  = "v1.0.0-alpha1";

        return JsonResponse::withJson($response, $result, 200);
    }

    public function testConnectFetchData(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['message']  = "Data ditemukan";
        $result['data']     = $this->userModel->countUser();

        return JsonResponse::withJson($response, $result, 200);
    }

    public function testNotification(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['message']  = "Data ditemukan";
        // $result['data']     = $this->general->sendNotificationToSlack('Halo dari Slim PHP 🎉 http://localhost:5173/#/task/detail/4');
        $result['data']     = $this->general->sendMessagePrivateSchedule(
            'cecepfahriazal1997@gmail.com',
            'Hei ada task baru lhoo 10 detik!!',
            time() + 15,
        );

        return JsonResponse::withJson($response, $result, 200);
    }

    public function testEnv(Request $request, Response $response): Response
    {
        $result['status']   = true;
        $result['message']  = "Data ditemukan";
        $result['data']     = $_ENV ?? $_SERVER['APP_FRONTEND_URL'];

        return JsonResponse::withJson($response, $result, 200);
    }
}
