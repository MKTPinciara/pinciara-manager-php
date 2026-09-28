<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PinciaraSeeder extends Seeder
{
    public function run()
    {
        // 1. Usuários
        $users = [
            [
                'nome'       => 'Visitante',
                'email'      => 'visitante@pi.com',
                'password'   => password_hash('visitante', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nome'       => 'Bernardo Pinciara',
                'email'      => 'bernardo.pinciara@gmail.com',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($users as $user) {
            $existing = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            if (!$existing) {
                $this->db->table('users')->insert($user);
            }
        }

        // 2. Imóveis de Exemplo
        $imoveis = [
            [
                'titulo'     => 'Apartamento Alto Padrão - Icaraí',
                'descricao'  => 'Excelente apartamento 3 quartos, 1 suíte, vista lateral mar e 2 vagas.',
                'endereco'   => 'Rua Tavares de Macedo, Icaraí - Niterói/RJ',
                'status'     => 'cadastrar',
                'imagens'    => json_encode(['https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&auto=format&fit=crop&q=80']),
                'video'      => 'https://youtube.com',
                'ordem'      => 0,
                'usuario_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'     => 'Cobertura Duplex com Piscina',
                'descricao'  => 'Cobertura reformada com área gourmet privativa e sol da manhã.',
                'endereco'   => 'Av. Jornalista Alberto Torres, Icaraí - Niterói/RJ',
                'status'     => 'fazer tour 360º',
                'imagens'    => json_encode(['https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=600&auto=format&fit=crop&q=80']),
                'video'      => null,
                'ordem'      => 1,
                'usuario_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'     => 'Casa em Condomínio Fechado',
                'descricao'  => 'Casa moderna de 4 suítes com quintal amplo e churrasqueira.',
                'endereco'   => 'Região Oceânica - Niterói/RJ',
                'status'     => 'fazer video',
                'imagens'    => json_encode(['https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=600&auto=format&fit=crop&q=80']),
                'video'      => 'https://youtube.com',
                'ordem'      => 2,
                'usuario_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'     => 'Sala Comercial Pronta para Uso',
                'descricao'  => 'Sala com 45m², recepção, lavabo e vaga de garagem rotativa.',
                'endereco'   => 'Rua Moreira César, Icaraí - Niterói/RJ',
                'status'     => 'concluído',
                'imagens'    => json_encode(['https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&auto=format&fit=crop&q=80']),
                'video'      => null,
                'ordem'      => 3,
                'usuario_id' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($imoveis as $imovel) {
            $existing = $this->db->table('imoveis')->where('titulo', $imovel['titulo'])->get()->getRow();
            if (!$existing) {
                $this->db->table('imoveis')->insert($imovel);
            }
        }

        // 3. Placas de Exemplo
        $placas = [
            [
                'titulo'      => 'Placa Vende-se - Icaraí',
                'largura'     => 1.20,
                'altura'      => 0.80,
                'material'    => 'Polionda',
                'tipo'        => 'Vende-se',
                'quantidade'  => 5,
                'observacao'  => 'Com ilhós nos quatro cantos e telefone da filial.',
                'status'      => 'produzir',
                'data_envio'  => date('Y-m-d', strtotime('+3 days')),
                'valor'       => 250.00,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Faixa Aluga-se 2.0x1.0',
                'largura'     => 2.00,
                'altura'      => 1.00,
                'material'    => 'Lona com Reforço',
                'tipo'        => 'Aluga-se',
                'quantidade'  => 2,
                'observacao'  => 'Lona com acabamento para madeira.',
                'status'      => 'pagar',
                'data_envio'  => date('Y-m-d'),
                'valor'       => 180.00,
                'created_at'  => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Placas Pequenas de Varanda',
                'largura'     => 0.60,
                'altura'      => 0.40,
                'material'    => 'PVC 2mm',
                'tipo'        => 'Vende-se',
                'quantidade'  => 10,
                'observacao'  => 'Lote pago na gráfica central.',
                'status'      => 'pago',
                'data_envio'  => date('Y-m-d', strtotime('-2 days')),
                'valor'       => 350.00,
                'created_at'  => date('Y-m-d H:i:s', strtotime('-4 days')),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Placas Padrão em Estoque',
                'largura'     => 1.00,
                'altura'      => 0.70,
                'material'    => 'Polionda',
                'tipo'        => 'Vende-se',
                'quantidade'  => 8,
                'observacao'  => 'Prontas na recepção para os corretores.',
                'status'      => 'disponíveis',
                'data_envio'  => date('Y-m-d', strtotime('-10 days')),
                'valor'       => 320.00,
                'created_at'  => date('Y-m-d H:i:s', strtotime('-10 days')),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'titulo'      => 'Placa Fachada Edifício Solar',
                'largura'     => 1.50,
                'altura'      => 1.00,
                'material'    => 'ACM',
                'tipo'        => 'Exclusividade',
                'quantidade'  => 1,
                'observacao'  => 'Instalada no prédio na Rua Miguel de Frias.',
                'status'      => 'usadas',
                'data_envio'  => date('Y-m-d', strtotime('-20 days')),
                'valor'       => 290.00,
                'created_at'  => date('Y-m-d H:i:s', strtotime('-20 days')),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($placas as $placa) {
            $existing = $this->db->table('placas')->where('titulo', $placa['titulo'])->get()->getRow();
            if (!$existing) {
                $this->db->table('placas')->insert($placa);
            }
        }
    }
}
