<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use App\Model\AuthModel;
use Pimple\Psr11\Container;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Auth
{
    private $container;
    private $authModel;

    public function __construct(Container $container)
    {
        $this->container    = $container;
        $this->authModel    = new AuthModel($this->container->get('db'));
    }

    public function signin(Request $request, Response $response): Response
    {
        $result         = array();
        $post           = $request->getParsedBody();
        $username       = isset($post["username"]) ? $post["username"] : '';
        $password       = isset($post["password"]) ? $post["password"] : '';
        $data           = $this->authModel->processLogin($username, $password);

        $result['status']  = $data['status'];
        $result['message'] = $data['message'];
        $result['token']   = (isset($data['token']) ? $data['token'] : array());

        return JsonResponse::withJson($response, $result, 200);

    }
}
