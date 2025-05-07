<?php

namespace App\Helper;

use Pimple\Psr11\Container;
use Slim\Views\Twig;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Psr\Http\Message\ServerRequestInterface as Request;

final class General {
    private Container $container;
    private array $request;

    public function __construct(Container $container, Request $request=null) {
        $this->container = $container;
        if ($request)
            $this->request = $request;
    }

    function replaceNullInArray(array $array, $replacement='') {
        array_walk_recursive($array, function (&$item) use ($replacement) {
            if (is_null($item) || $item == 'null') {
                $item = $replacement;
            }
        });
        return $array;
    }

    public function baseUrl($extended_url="") {

        $http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
        $newurl = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);


        $domain = $_SERVER['HTTP_HOST'];

        if (strpos($domain, 'dev') !== false || strpos($domain, 'localhost') !== false) {
            if($_SERVER['SERVER_NAME'] == 'localhost') {
                $baseUrl    = "$http" . $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'] . "" . $newurl;
            } else {
                $baseUrl    = "$http" . $_SERVER['SERVER_NAME'] . "" . $newurl;
            }
        } else {
            $baseUrl = 'https://pamsimas-panembangan-be-production.up.railway.app/';
        }
        
        return $baseUrl.''.$extended_url;
    }

    public function formatDate($waktu, $tampilkan_jam = false)
    {
        $tanggal = date('j', strtotime($waktu));
        $bulan_array = array(
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        );
        $bl = date('n', strtotime($waktu));
        $bulan = substr($bulan_array[$bl], 0, 3);
        $tahun = date('Y', strtotime($waktu));
        $jam = date('H:i', strtotime($waktu));

        if(empty($tampilkan_jam)) {
            return "$tanggal $bulan $tahun";
        } else {
            return "$tanggal $bulan $tahun" . " " . $jam;
        }
    }
}

?>