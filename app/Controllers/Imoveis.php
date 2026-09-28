<?php

namespace App\Controllers;

use App\Models\ImovelModel;
use CodeIgniter\Controller;

class Imoveis extends Controller
{
    protected $imovelModel;
    protected $session;

    public function __construct()
    {
        $this->imovelModel = new ImovelModel();
        $this->session     = session();
    }

    public function index()
    {
        $tab = $this->request->getGet('tab') ?? 'cadastrar';
        $tab = strtolower(trim($tab));

        $tabs = [
            'cadastrar'        => 'Cadastrar',
            'fazer tour 360º'  => 'Tour 360º',
            'fazer video'      => 'Vídeo',
            'concluído'        => 'Concluído',
        ];

        // Se a aba informada for inválida, assume a primeira
        if (!array_key_exists($tab, $tabs)) {
            $tab = 'cadastrar';
        }

        // Contagens por aba
        $counts = [];
        foreach (array_keys($tabs) as $key) {
            $counts[$key] = $this->imovelModel->where('LOWER(status)', $key)->countAllResults();
        }

        // Imóveis da aba selecionada
        $imoveis = $this->imovelModel->getByStatus($tab);

        // Decodificar imagens em array para cada imóvel
        foreach ($imoveis as &$imovel) {
            $decoded = json_decode($imovel['imagens'] ?? '[]', true);
            $imovel['imagens_array'] = is_array($decoded) ? $decoded : [];
        }

        return view('imoveis/index', [
            'title'     => 'Esteira de Imóveis | Pinciara Manager',
            'activeNav' => 'imoveis',
            'tabs'      => $tabs,
            'activeTab' => $tab,
            'counts'    => $counts,
            'imoveis'   => $imoveis,
        ]);
    }

    public function store()
    {
        $titulo    = trim($this->request->getPost('titulo') ?? '');
        $descricao = trim($this->request->getPost('descricao') ?? '');
        $endereco  = trim($this->request->getPost('endereco') ?? '');
        $status    = strtolower(trim($this->request->getPost('status') ?? 'cadastrar'));
        $video     = trim($this->request->getPost('video') ?? '');
        $imagemUrl = trim($this->request->getPost('imagem_url') ?? '');

        if (empty($titulo)) {
            return redirect()->back()->withInput()->with('error', 'O título do imóvel é obrigatório.');
        }

        $imagens = [];

        // Upload de arquivos
        $files = $this->request->getFiles();
        if (isset($files['fotos'])) {
            $uploadPath = FCPATH . 'uploads/imoveis';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files['fotos'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $imagens[] = base_url('uploads/imoveis/' . $newName);
                }
            }
        }

        // Se o usuário passou uma URL direta de imagem
        if (!empty($imagemUrl)) {
            $imagens[] = $imagemUrl;
        }

        // Se nenhuma foto foi enviada, imagem padrão amigável
        if (empty($imagens)) {
            $imagens[] = 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80';
        }

        // Ordem: coloca como o primeiro da esteira
        $minOrdem = $this->imovelModel->selectMin('ordem')->first();
        $novaOrdem = ($minOrdem && isset($minOrdem['ordem'])) ? ($minOrdem['ordem'] - 1) : 0;

        $this->imovelModel->insert([
            'titulo'     => $titulo,
            'descricao'  => $descricao,
            'endereco'   => $endereco,
            'status'     => $status,
            'imagens'    => json_encode($imagens),
            'video'      => !empty($video) ? $video : null,
            'ordem'      => $novaOrdem,
            'usuario_id' => $this->session->get('userId') ?? 1,
        ]);

        return redirect()->to('/imoveis?tab=' . urlencode($status))->with('success', 'Imóvel criado com sucesso!');
    }

    public function updateStatus($id)
    {
        $status = strtolower(trim($this->request->getPost('status') ?? ''));
        if (empty($status)) {
            return redirect()->back()->with('error', 'Status inválido.');
        }

        $this->imovelModel->update($id, ['status' => $status]);
        return redirect()->to('/imoveis?tab=' . urlencode($status))->with('success', 'Status do imóvel atualizado!');
    }

    public function update($id)
    {
        $imovel = $this->imovelModel->find($id);
        if (!$imovel) {
            return redirect()->back()->with('error', 'Imóvel não encontrado.');
        }

        $titulo    = trim($this->request->getPost('titulo') ?? '');
        $descricao = trim($this->request->getPost('descricao') ?? '');
        $endereco  = trim($this->request->getPost('endereco') ?? '');
        $status    = strtolower(trim($this->request->getPost('status') ?? $imovel['status']));
        $video     = trim($this->request->getPost('video') ?? '');

        if (empty($titulo)) {
            return redirect()->back()->with('error', 'Título não pode ser vazio.');
        }

        $imagens = json_decode($imovel['imagens'] ?? '[]', true) ?: [];

        // Upload de novas fotos adicionais se houver
        $files = $this->request->getFiles();
        if (isset($files['fotos'])) {
            $uploadPath = FCPATH . 'uploads/imoveis';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($files['fotos'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $imagens[] = base_url('uploads/imoveis/' . $newName);
                }
            }
        }

        $this->imovelModel->update($id, [
            'titulo'    => $titulo,
            'descricao' => $descricao,
            'endereco'  => $endereco,
            'status'    => $status,
            'video'     => !empty($video) ? $video : null,
            'imagens'   => json_encode($imagens),
        ]);

        return redirect()->to('/imoveis?tab=' . urlencode($status))->with('success', 'Imóvel atualizado!');
    }

    public function updateOrder()
    {
        $json = $this->request->getJSON(true);
        $order = $json['order'] ?? $this->request->getPost('order');

        if (is_array($order)) {
            foreach ($order as $index => $id) {
                $this->imovelModel->update($id, ['ordem' => $index]);
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'Ordem atualizada com sucesso']);
        }

        return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Lista inválida']);
    }

    public function delete($id)
    {
        $imovel = $this->imovelModel->find($id);
        if ($imovel) {
            $status = $imovel['status'];
            $this->imovelModel->delete($id);
            return redirect()->to('/imoveis?tab=' . urlencode($status))->with('success', 'Imóvel excluído com sucesso.');
        }

        return redirect()->back()->with('error', 'Imóvel não encontrado.');
    }
}
