<?php

declare(strict_types=1);

namespace App\Model;

use Pimple\Psr11\Container;
use Exception;

use App\Helper\General;

final class TaskModel
{
    protected $database, $general;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(Container $container)
    {
        $this->database         = $container->get('db');
        $this->general          = new General($container);
    }

    public function buildQueryList($params=null) {
        $getQuery = $this->db()->table('tugas');
        $getQuery->select($getQuery->raw('tugas.*, tugas_penerima.user_id as penerima_tugas_id'));
        $getQuery->join('tugas_penerima', 'tugas.id', '=', 'tugas_penerima.tugas_id');

        if (!empty($params['user_id'])) {
            $getQuery->where(function(\Pecee\Pixie\QueryBuilder\QueryBuilderHandler $qb) use ($params) {
                $qb->where('tugas.user_id', $params['user_id']);
                $qb->orWhere('tugas_penerima.user_id', $params['user_id']);
            });
        }
        
        if(!empty($params['prioritas'])) {
            $getQuery->where('prioritas', $params['prioritas']);
        } if(!empty($params['status'])) {
            $getQuery->where('status', $params['status']);
        } if(!empty($params['dateRange'])) {
            $tmpDateRange = explode(' to ', $params['dateRange']);
            $getQuery->whereBetween('deadline', ...$tmpDateRange);
        } if(!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where('deskripsi', 'LIKE', "%$keywords%");
        }

        $getQuery->orderBy('tugas.deadline', 'asc');

        return $getQuery;
    }

    public function list($params)
    {
        $getQuery = $this->buildQueryList($params);
        
        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $list = $getQuery->get();

        foreach ($list as $row) {
            $getPemberiTugas = $this->db()->table('users')->where('id', $row->user_id)->first();
            $getPenerimaTugas = $this->db()->table('users')->where('id', $row->penerima_tugas_id)->first();
            $row->pemberi_tugas = $getPemberiTugas->nama ?? '';
            $row->penerima_tugas = $getPenerimaTugas->nama ?? '';
            $row->is_own = true;

            if (!empty($params['user_id']))
                $row->is_own = $row->user_id == $params['user_id'];
        }

        return ['data' => $list, 'total' => $totalData];
    }

    public function detail($id) {
        $result = $this->db()->table('tugas')
                    ->select($this->db()->raw('*'))
                    ->where('id', $id)
                    ->first();

        if (!empty($result)) {
            $getDetailTask = $this->db()->table('tugas_penerima')->where('tugas_id', $id)->first();
            
            $getPemberiTugas = $this->db()->table('users')->where('id', $result->user_id)->first();
            $getPenerimaTugas = $this->db()->table('users')->where('id', $getDetailTask->user_id)->first();

            $result->tugas_detail_id = $getDetailTask->id;
            $result->penerima_tugas_id = $getDetailTask->user_id;
            $result->pemberi_tugas = $getPemberiTugas->nama;
            $result->penerima_tugas = $getPenerimaTugas->nama;
        }

        return $result;
    }

    public function save($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $data = [
            'user_id' => $params['user_id'],
            'deskripsi' => $params['deskripsi'],
            'prioritas' => $params['prioritas'],
            'deadline' => $params['deadline'],
            'catatan' => $params['catatan'],
        ];

        $lastId = null;

        if (empty($params['id'])) {
            $data = array_merge(['created_at' => date('Y-m-d H:i:s')], $data);
            $lastId = $this->db()->table('tugas')->insert($data);
        } else {
            $lastId = $params['id'];
            $this->db()->table('tugas')->where('id', $params['id'])->update($data);
        }

        if (!empty($lastId)) {
            // save detail tugas
            $dataDetail = [
                'tugas_id' => $lastId,
                'user_id' => $params['penerima_tugas_id'],
            ];

            if (!empty($params['tugas_detail_id'])) {
                $process = $this->db()->table('tugas_penerima')->where('id', $params['tugas_detail_id'])->update($dataDetail);
            } else {
                $dataDetail = array_merge(['created_at' => date('Y-m-d H:i:s')], $dataDetail);
                $process = $this->db()->table('tugas_penerima')->insert($dataDetail);
            }

            if ($process) {
                $detalPemberiTugas = $this->db()->table('users')->where('id', $params['user_id'])->first();
                $detailPenerima = $this->db()->table('users')->where('id', $params['penerima_tugas_id'])->first();
                // send notification
                $bodyMessage =  "📌 *Notifikasi Tugas Baru*\n" .
                                "▪ *Pemberi Tugas:* " . $detalPemberiTugas->nama . "\n" .
                                "▪ *Deskripsi Tugas:* " . $params['deskripsi'] . "\n" .
                                "▪ *Prioritas:* *" . strtoupper($params['prioritas']) . "*\n" .
                                "▪ *Deadline:* ". $this->general->formatDate($params['deadline'], true) ." WIB\n" .
                                "🔗 <". ($_ENV['APP_FRONTEND_URL'] ?: $_SERVER['APP_FRONTEND_URL']) .'/#/task/detail/' . $lastId ."|Lihat Detail Tugas>";
                $this->general->sendMessagePrivate($detailPenerima->email, $bodyMessage);
            }

            $result                 = ['status' => true, 'message' => 'Data berhasil disimpan'];
        }

        return $result;
    }

    public function updateStatus($id, $status) {
        $result                 = ['status' => false, 'message' => 'Data gagal diperbaharui'];

        $checkData = $this->db()->table('tugas')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->db()->table('tugas')->where('id', $id)->update(['status' => $status]);

            if ($process) {
                if ($status == 'done') {
                    $checkDataPenerima = $this->db()->table('tugas_penerima')->where('id', $id)->first();
                    if (!empty($checkDataPenerima)) {
                        $detailPenerima = $this->db()->table('users')->where('id', $checkDataPenerima->user_id)->first();

                        $date = $this->general->formatDate(date('Y-m-d H:i:s'), true);
                        $link = ($_ENV['APP_FRONTEND_URL'] ?: $_SERVER['APP_FRONTEND_URL']) .'/#/task/detail/' . $id;
                        
                        $bodyMessage = $text = "📌 *Notifikasi Tugas Selesai*\n" .
                                                "▪ *Deskripsi Tugas:* $checkData->deskripsi\n" .
                                                "▪ *Diselesaikan oleh:* $detailPenerima->nama\n" .
                                                "▪ *Tanggal & Waktu:* $date\n" .
                                                "▪ *Status Tugas:* *Berhasil dikerjakan*\n" .
                                                "🔍 <$link|Lihat Detail Tugas>";
                        // send notification
                        $this->general->sendMessagePrivate($detailPenerima->email, $bodyMessage);
                    }
                }

                $result                 = ['status' => true, 'message' => 'Data berhasil diperbaharui'];
            }
        }

        return $result;
    }

    public function delete($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('tugas')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->db()->table('tugas')->where('id', $id)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('tugas')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $process = $this->db()->table('tugas')->whereIn('id', $listId)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function listDiscussion($userId, $taskId) {
        $list = $this->db()->table('diskusi')
                        ->select($this->db()->raw('diskusi.*, users.nama as nama_user'))
                        ->join('users', 'users.id', '=', 'diskusi.user_id')
                        ->where('tugas_id', $taskId)
                        ->orderBy('created_at', 'asc')
                        ->get();

        foreach ($list as $row) {
            $row->is_own = $userId == $row->user_id;
        }

        return $list;
    }

    public function saveDiscussion($taskId, $userId, $message) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];
        $process = $this->db()->table('diskusi')->insert([
            'tugas_id' => $taskId,
            'user_id' => $userId,
            'pesan' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if (!empty($process)) {
            $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
        }

        return $result;
    }

    public function statistic($userId) {
        $getQuery = $this->db()->table('tugas');
        $getQuery->select($getQuery->raw('tugas.*, tugas_penerima.user_id as penerima_tugas_id'));
        $getQuery->join('tugas_penerima', 'tugas.id', '=', 'tugas_penerima.tugas_id');
        
        if (!empty($userId)) {
            $getQuery->where(function(\Pecee\Pixie\QueryBuilder\QueryBuilderHandler $qb) use ($userId) {
                $qb->where('tugas.user_id', $userId);
                $qb->orWhere('tugas_penerima.user_id', $userId);
            });
        }

        $list = $getQuery->get();

        $statistic = [
            'todo' => 0,
            'progress' => 0,
            'done' => 0,
            'all' => 0
        ];

        if (!empty($list)) {
            foreach ($list as $row) {
                $statistic[$row->status]++;
                $statistic['all']++;
            }
        }

        return $statistic;
    }

    public function statisticByMonth($userId) {
        $getQuery = $this->db()->table('tugas');
        $getQuery->select($getQuery->raw('tugas.*, tugas_penerima.user_id as penerima_tugas_id'));
        $getQuery->join('tugas_penerima', 'tugas.id', '=', 'tugas_penerima.tugas_id');
        
        $getQuery->where($getQuery->raw('YEAR(tugas.created_at)'), $getQuery->raw('YEAR(NOW())'));
        
        if (!empty($params['user_id'])) {
            $getQuery->where(function(\Pecee\Pixie\QueryBuilder\QueryBuilderHandler $qb) use ($params) {
                $qb->where('tugas.user_id', $params['user_id']);
                $qb->orWhere('tugas_penerima.user_id', $params['user_id']);
            });
        }

        $list = $getQuery->get();

        $statistic = [];

        foreach ($list as $row) {
            $month  = (int) date('m', strtotime($row->created_at));

            if (!isset($statistic[$month]))
                $statistic[$month] = 0;
            
            $statistic[$month]++;
        }

        return $statistic;
    }
}
