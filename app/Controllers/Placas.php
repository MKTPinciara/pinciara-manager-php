<?php

namespace App\Controllers;

use App\Models\PlacaModel;
use CodeIgniter\Controller;

class Placas extends Controller
{
    protected $placaModel;
    protected $session;

    public function __construct()
    {
        $this->placaModel = new PlacaModel();
        $this->session    = session();
    }

    public function index()
    {
        $tab = $this->request->getGet('tab') ?? 'produzir';
        $tab = strtolower(trim($tab));

        $tabs = [
            'produzir'    => 'Produzir',
            'pagar'       => 'Pagar',
            'pago'        => 'Pago',
            'disponíveis' => 'Disponíveis',
            'usadas'      => 'Usadas',
        ];

        if (!array_key_exists($tab, $tabs)) {
            $tab = 'produzir';
        }

        // Contagens por aba
        $counts = [];
        foreach (array_keys($tabs) as $key) {
            $counts[$key] = $this->placaModel->where('LOWER(status)', $key)->countAllResults();
        }

        // Totais financeiros e contagens gerais
        $totals = $this->placaModel->getTotals();

        // Placas da aba selecionada
        $placas = $this->placaModel->getByStatus($tab);

        return view('placas/index', [
            'title'     => 'Controle de Placas | Pinciara Manager',
            'activeNav' => 'placas',
            'tabs'      => $tabs,
            'activeTab' => $tab,
            'counts'    => $counts,
            'totals'    => $totals,
            'placas'    => $placas,
        ]);
    }

    public function store()
    {
        $titulo     = trim($this->request->getPost('titulo') ?? '');
        $largura    = (float)str_replace(',', '.', $this->request->getPost('largura') ?? '0');
        $altura     = (float)str_replace(',', '.', $this->request->getPost('altura') ?? '0');
        $material   = trim($this->request->getPost('material') ?? 'Polionda');
        $tipo       = trim($this->request->getPost('tipo') ?? 'Vende-se');
        $quantidade = (int)($this->request->getPost('quantidade') ?? 1);
        $observacao = trim($this->request->getPost('observacao') ?? '');
        $status     = strtolower(trim($this->request->getPost('status') ?? 'produzir'));
        $dataEnvio  = $this->request->getPost('data_envio') ?: null;
        $valor      = (float)str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $this->request->getPost('valor') ?? '0');

        if (empty($titulo)) {
            return redirect()->back()->withInput()->with('error', 'O título da placa é obrigatório.');
        }

        // Cálculo de valor automático se não informado (R$ 35,00/m²)
        if ($valor <= 0 && $largura > 0 && $altura > 0) {
            $valor = round($largura * $altura * 35.00 * max(1, $quantidade), 2);
        }

        $this->placaModel->insert([
            'titulo'     => $titulo,
            'largura'    => $largura,
            'altura'     => $altura,
            'material'   => $material,
            'tipo'       => $tipo,
            'quantidade' => max(1, $quantidade),
            'observacao' => $observacao,
            'status'     => $status,
            'data_envio' => $dataEnvio,
            'valor'      => $valor,
        ]);

        return redirect()->to('/placas?tab=' . urlencode($status))->with('success', 'Placa cadastrada com sucesso!');
    }

    public function updateStatus($id)
    {
        $status = strtolower(trim($this->request->getPost('status') ?? ''));
        if (empty($status)) {
            return redirect()->back()->with('error', 'Status inválido.');
        }

        $placa = $this->placaModel->find($id);
        if (!$placa) {
            return redirect()->back()->with('error', 'Placa não encontrada.');
        }

        $data = ['status' => $status];

        // Ao enviar para pagar, calcula valor e define data de envio se ainda não existirem
        if ($status === 'pagar') {
            if (empty($placa['data_envio'])) {
                $data['data_envio'] = date('Y-m-d');
            }
            if ((float)$placa['valor'] <= 0 && (float)$placa['largura'] > 0 && (float)$placa['altura'] > 0) {
                $data['valor'] = round((float)$placa['largura'] * (float)$placa['altura'] * 35.00 * (int)$placa['quantidade'], 2);
            }
        }

        $this->placaModel->update($id, $data);
        return redirect()->to('/placas?tab=' . urlencode($status))->with('success', 'Status da placa atualizado!');
    }

    public function usar($id)
    {
        $placa = $this->placaModel->find($id);
        if (!$placa) {
            return redirect()->back()->with('error', 'Placa não encontrada.');
        }

        $qtdUsada = (int)$this->request->getPost('quantidade_usada');
        if ($qtdUsada <= 0) {
            return redirect()->back()->with('error', 'Informe uma quantidade válida para uso.');
        }

        if ($qtdUsada > (int)$placa['quantidade']) {
            return redirect()->back()->with('error', 'Quantidade solicitada maior do que o estoque disponível (' . $placa['quantidade'] . ' un).');
        }

        $restante = (int)$placa['quantidade'] - $qtdUsada;

        if ($restante <= 0) {
            // Usou todo o lote disponível: move a placa inteira para "usadas"
            $this->placaModel->update($id, ['status' => 'usadas']);
        } else {
            // Atualiza o estoque restante do lote disponível
            $this->placaModel->update($id, ['quantidade' => $restante]);

            // Verifica se já existe lote similar em 'usadas' para somar quantidade
            $placaUsadaExistente = $this->placaModel
                ->where('titulo', $placa['titulo'])
                ->where('largura', $placa['largura'])
                ->where('altura', $placa['altura'])
                ->where('material', $placa['material'])
                ->where('tipo', $placa['tipo'])
                ->where('status', 'usadas')
                ->first();

            if ($placaUsadaExistente) {
                $this->placaModel->update($placaUsadaExistente['id'], [
                    'quantidade' => (int)$placaUsadaExistente['quantidade'] + $qtdUsada
                ]);
            } else {
                $valorProporcional = (float)$placa['valor'] > 0
                    ? round(((float)$placa['valor'] / max(1, (int)$placa['quantidade'])) * $qtdUsada, 2)
                    : round((float)$placa['largura'] * (float)$placa['altura'] * 35.00 * $qtdUsada, 2);

                $this->placaModel->insert([
                    'titulo'     => $placa['titulo'],
                    'largura'    => $placa['largura'],
                    'altura'     => $placa['altura'],
                    'material'   => $placa['material'],
                    'tipo'       => $placa['tipo'],
                    'quantidade' => $qtdUsada,
                    'observacao' => $placa['observacao'],
                    'status'     => 'usadas',
                    'data_envio' => $placa['data_envio'] ?? date('Y-m-d'),
                    'valor'      => $valorProporcional,
                ]);
            }
        }

        return redirect()->to('/placas?tab=usadas')->with('success', "Baixa realizada com sucesso! {$qtdUsada} unidade(s) movida(s) para 'Usadas'.");
    }

    public function update($id)
    {
        $placa = $this->placaModel->find($id);
        if (!$placa) {
            return redirect()->back()->with('error', 'Placa não encontrada.');
        }

        $titulo     = trim($this->request->getPost('titulo') ?? '');
        $largura    = (float)str_replace(',', '.', $this->request->getPost('largura') ?? '0');
        $altura     = (float)str_replace(',', '.', $this->request->getPost('altura') ?? '0');
        $material   = trim($this->request->getPost('material') ?? $placa['material']);
        $tipo       = trim($this->request->getPost('tipo') ?? $placa['tipo']);
        $quantidade = (int)($this->request->getPost('quantidade') ?? $placa['quantidade']);
        $observacao = trim($this->request->getPost('observacao') ?? '');
        $status     = strtolower(trim($this->request->getPost('status') ?? $placa['status']));
        $dataEnvio  = $this->request->getPost('data_envio') ?: null;
        $valor      = (float)str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $this->request->getPost('valor') ?? '0');

        if (empty($titulo)) {
            return redirect()->back()->with('error', 'Título não pode ser vazio.');
        }

        $this->placaModel->update($id, [
            'titulo'     => $titulo,
            'largura'    => $largura,
            'altura'     => $altura,
            'material'   => $material,
            'tipo'       => $tipo,
            'quantidade' => max(1, $quantidade),
            'observacao' => $observacao,
            'status'     => $status,
            'data_envio' => $dataEnvio,
            'valor'      => $valor,
        ]);

        return redirect()->to('/placas?tab=' . urlencode($status))->with('success', 'Placa atualizada com sucesso!');
    }

    public function delete($id)
    {
        $placa = $this->placaModel->find($id);
        if ($placa) {
            $status = $placa['status'];
            $this->placaModel->delete($id);
            return redirect()->to('/placas?tab=' . urlencode($status))->with('success', 'Placa excluída com sucesso.');
        }

        return redirect()->back()->with('error', 'Placa não encontrada.');
    }
}
