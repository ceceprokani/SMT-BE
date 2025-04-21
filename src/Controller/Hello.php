<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use App\Helper\TwigResponse;
use Pimple\Psr11\Container;

use App\Model\UserModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Hello
{
    private $container, $userModel;
    
    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->userModel    = new UserModel($this->container->get('db'));
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
}
