<?php

namespace App\Models;

use CodeIgniter\Model;

class PlacaModel extends Model
{
    protected $table            = 'placas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'titulo',
        'largura',
        'altura',
        'material',
        'tipo',
        'quantidade',
        'observacao',
        'status',
        'data_envio',
        'valor',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByStatus(string $status = null)
    {
        $builder = $this->builder();
        if ($status !== null && $status !== '') {
            $builder->where('LOWER(status)', strtolower(trim($status)));
        }
        return $builder->orderBy('created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function getTotals()
    {
        $builder = $this->builder();
        $row = $builder->selectSum('valor', 'total_gasto')
                       ->selectSum('quantidade', 'total_placas')
                       ->get()
                       ->getRowArray();

        return [
            'total_gasto'  => (float)($row['total_gasto'] ?? 0),
            'total_placas' => (int)($row['total_placas'] ?? 0),
        ];
    }
}
