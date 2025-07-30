<?php

namespace App\Helper;

use Pimple\Psr11\Container;
use Slim\Views\Twig;

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
            $baseUrl = $_ENV['APP_BASE_URL'] ?: $_SERVER['APP_BASE_URL'];
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
            return "$tanggal $bulan $tahun" . ", " . $jam;
        }
    }

    public function sendNotificationToSlack($text) {
        $webhookUrl = 'https://hooks.slack.com/services/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';
        // $webhookUrl = 'https://hooks.slack.com/services/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';

        $payload = json_encode([
            'text' => $text,
            'username' => 'BOT SMT',
        ]);

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function sendMessagePrivate($email, $text) {
        $token = $_ENV['SLACK_KEY'] ?: $_SERVER['SLACK_KEY'];

        $ch = curl_init('https://slack.com/api/users.list');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        // Misalnya cari user dengan email tertentu
        foreach ($data['members'] as $user) {
            if (!empty($user['profile']['email']) && $user['profile']['email'] === $email) {
                $payload = json_encode([
                    'channel' => $user['id'],
                    'text' => $text,
                ]);

                $ch = curl_init('https://slack.com/api/chat.postMessage');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer $token",
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $result = curl_exec($ch);
                curl_close($ch);

                break;
            }
        }
    }
    public function sendMessagePrivateSchedule($email, $text, $time) {
        $token = $_ENV['SLACK_KEY'] ?: $_SERVER['SLACK_KEY'];
        // 1. Dapatkan user ID dari email
        $ch = curl_init('https://slack.com/api/users.lookupByEmail?email=' . urlencode($email));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $user = json_decode($response, true);
        $userId = $user['user']['id'];

        // 2. Buka DM channel
        $ch = curl_init('https://slack.com/api/conversations.open');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['users' => $userId]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $dmChannel = json_decode($response, true);
        $channelId = $dmChannel['channel']['id'];

        // 3. Jadwalkan pesan
        $payload = [
            'channel' => $channelId,
            'text' => $text,
            'post_at' => $time
        ];

        $ch = curl_init('https://slack.com/api/chat.scheduleMessage');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}

?>