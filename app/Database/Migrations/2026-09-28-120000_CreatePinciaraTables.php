<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePinciaraTables extends Migration
{
    public function up()
    {
        // 1. Tabela Users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. Tabela Imoveis
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '64',
                'null'       => true,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'endereco' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'cadastrar',
            ],
            'imagens' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'video' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ordem' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('imoveis', true);

        // 3. Tabela Placas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type'       => 'VARCHAR',
                'constraint' => '64',
                'null'       => true,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'largura' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'altura' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'material' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'Lona',
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'Vende-se',
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'observacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'produzir',
            ],
            'data_envio' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('placas', true);
    }

    public function down()
    {
        $this->forge->dropTable('placas', true);
        $this->forge->dropTable('imoveis', true);
        $this->forge->dropTable('users', true);
    }
}
