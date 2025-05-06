<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

final class UserModel
{
    protected $database;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(\Pecee\Pixie\Connection $database)
    {
        $this->database       = $database;
    }

    public function countUser()
    {
        return $this->db()->table('users')->count();
    }

    public function buildQueryList($params=null) {
        $getQuery = $this->db()->table('users');
        $getQuery->select($getQuery->raw('users.*, jabatan.nama as jabatan'));
        $getQuery->where('users.role', '!=', 'superadmin');
        $getQuery->leftJoin('jabatan', 'jabatan.id', '=', 'users.jabatan_id');
        
        if(!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where('users.nama', 'LIKE', "%$keywords%");
        }

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

        $getQuery->orderBy("nama", "asc"); 
        
        $list = $getQuery->get();

        return ['data' => $list, 'total' => $totalData];
    }

    public function detail($id) {
        $result = $this->db()->table('users')
                    ->select($this->db()->raw('id, nama, email, telepon, jabatan_id, alamat, status'))
                    ->where('id', $id)
                    ->first();

        return $result;
    }

    public function save($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $checkUser = $this->db()->table('users')->where('username', $params['email'])->where('id', '!=', $params['id'])->first();
        
        if (!empty($checkUser)) {
            $result['message'] = 'Email sudah terdaftar disistem! Silahkan gunakan email lain';
        } else {
            // register user auth
            $dataUser = [
                'username' => $params['email'],
                'role' => $params['role'],
                'nama' => $params['nama'],
                'email' => $params['email'],
                'telepon' => $params['telepon'],
                'jabatan_id' => $params['jabatan_id'],
                'alamat' => $params['alamat'],
            ];

            if (!empty($params['password'])) {
                $dataUser = array_merge([
                    'password' => password_hash($params['password'], PASSWORD_BCRYPT),
                    'password_raw' => $params['password'],
                ], $dataUser);
            }

            if (empty($params['id'])) {
                $dataUser = array_merge(['created_at' => date('Y-m-d H:i:s')], $dataUser);
                $process = $this->db()->table('users')->insert($dataUser);
            } else {
                $process = $params['id'];
                $this->db()->table('users')->where('id', $params['id'])->update($dataUser);
            }

            if (!empty($process)) {
                $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];
            }
        }

        return $result;
    }

    public function delete($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('users')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->db()->table('users')->where('id', $id)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('users')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $process = $this->db()->table('users')->whereIn('id', $listId)->delete();

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }
}
