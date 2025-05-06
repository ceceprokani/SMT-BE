<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

final class MasterDataModel
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

    public function listJabatan() {
        $result = $this->db()->table('jabatan')->get();

        return $result;
    }

    public function listUser() {
        $result = $this->db()->table('users')->select('id', 'nama')->where('role', 'staff')->orderBy('nama', 'asc')->get();

        return $result;
    }
}
