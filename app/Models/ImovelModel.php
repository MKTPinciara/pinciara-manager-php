<?php

namespace App\Models;

use CodeIgniter\Model;

class ImovelModel extends Model
{
    protected $table            = 'imoveis';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'titulo',
        'descricao',
        'endereco',
        'status',
        'imagens',
        'video',
        'ordem',
        'usuario_id',
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
        return $builder->orderBy('ordem', 'ASC')
                       ->orderBy('created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }

    public function updateOrdem(array $items)
    {
        foreach ($items as $index => $id) {
            $this->update($id, ['ordem' => $index]);
        }
    }
}
