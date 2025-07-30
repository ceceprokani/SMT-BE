<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\TaskModel;

use App\Helper\General;

use Twig\Extra\Intl\IntlExtension;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Report
{
    private $container;
    private $auth;
    private $user;
    private $model;
    private $general;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->model                = new TaskModel($this->container);
        $this->general              = new General($this->container);
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

        if (!in_array($this->user->role, ['superadmin', 'admin']))
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

    public function download(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'orientation' => 'L',
            'margin_left' => 10,   // Margin kiri dalam mm
            'margin_right' => 10,  // Margin kanan dalam mm
            'margin_top' => 10,    // Margin atas dalam mm
            'margin_bottom' => 10, // Margin bawah dalam mm
            'tempDir' => __DIR__ . '/../../public/uploads',
        ]);

        $mpdf->charset_in='utf-8';
        $mpdf->useSubstitutions = true;

        $view            = \Slim\Views\Twig::fromRequest($request);
        if (!$view->getEnvironment()->hasExtension('Twig\Extra\Intl\IntlExtension')) {
            $view->addExtension(new IntlExtension());
        }

        if ($this->user->role != 'superadmin') {
            $params['user_id'] = $this->user->id;
        }

        $list   = $this->model->list($params);
        $data           = [
            'data' => $list,
        ];

        $htmlString     = $view->fetch('pdf/report.twig', $data);
        
        $logoPath     = __DIR__ . '/../../public/assets/images/logo.png';

        if (file_exists($logoPath)) {
            $mpdf->imageVars['logo'] = file_get_contents($logoPath);
        }

        $mpdf->PageNumSubstitutions[] = [
            'from' => 4,
            'reset' => 1,
            'type' => '1',
            'suppress' => 'off'
        ];
        $mpdf->WriteHTML($htmlString);
        $mpdf->Output("LAPORAN.pdf","F");

        return JsonResponse::withJson($response, ['url' => $this->general->baseUrl('LAPORAN.pdf')], 200);
    }
}
