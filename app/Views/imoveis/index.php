<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="{ 
    modalNovo: false, 
    modalEditar: false, 
    modalGaleria: false,
    galeriaImovel: { titulo: '', imagens: [] },
    search: '',
    orderSavedToast: false,
    imovelEdit: { id: '', titulo: '', endereco: '', descricao: '', status: '', video: '' },
    init() {
        this.$nextTick(() => {
            const el = document.getElementById('sortable-cards');
            if (el && window.Sortable) {
                Sortable.create(el, {
                    animation: 200,
                    handle: '.drag-handle',
                    ghostClass: 'opacity-40',
                    onEnd: () => {
                        const order = Array.from(el.querySelectorAll('[data-id]')).map(item => item.dataset.id);
                        fetch('<?= base_url('imoveis/order') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ order: order })
                        }).then(res => res.json()).then(data => {
                            this.orderSavedToast = true;
                            setTimeout(() => { this.orderSavedToast = false; }, 3000);
                        }).catch(err => console.error('Erro ao reordenar:', err));
                    }
                });
            }
        });
    }
}">

    <!-- Cabeçalho da Página -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Esteira de Imóveis</h1>
            <p class="text-sm text-slate-500 mt-1">Gerencie a produção e o fluxo de captação e mídia dos imóveis da imobiliária.</p>
        </div>
        <div>
            <button @click="modalNovo = true" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm shadow-md shadow-brand-600/20 transition-all transform active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Novo Imóvel
            </button>
        </div>
    </div>

    <!-- Navegação de Abas (Etapas da Esteira) e Pesquisa Rápida -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 mb-8 pb-3">
        <nav class="flex space-x-2 sm:space-x-4 min-w-max overflow-x-auto pb-1" aria-label="Abas da Esteira">
            <?php foreach ($tabs as $key => $label): ?>
                <?php 
                    $isActive = ($activeTab === $key);
                    $count = $counts[$key] ?? 0;
                ?>
                <a href="<?= base_url('imoveis?tab=' . urlencode($key)) ?>" 
                   class="flex items-center gap-2 py-2.5 px-4 border-b-2 font-semibold text-sm transition-all whitespace-nowrap <?= $isActive ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
                    <span><?= esc($label) ?></span>
                    <span class="px-2 py-0.5 text-xs rounded-full font-bold <?= $isActive ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-600' ?>">
                        <?= $count ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Campo de Pesquisa em Tempo Real -->
        <div class="relative w-full sm:w-72">
            <input type="text" x-model="search" placeholder="🔍 Filtrar imóveis nesta etapa..." 
                   class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-hidden focus:ring-2 focus:ring-brand-500 shadow-xs">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <!-- Lista / Cards de Imóveis da Aba Ativa -->
    <?php if (empty($imoveis)): ?>
        <div class="text-center py-16 px-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Nenhum imóvel nesta etapa</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">Não há nenhum imóvel cadastrado na etapa "<?= esc($tabs[$activeTab] ?? $activeTab) ?>".</p>
            <button @click="modalNovo = true" class="mt-4 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                + Adicionar Imóvel
            </button>
        </div>
    <?php else: ?>
        <div id="sortable-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($imoveis as $imovel): ?>
                <?php 
                    $coverImage = !empty($imovel['imagens_array']) ? $imovel['imagens_array'][0] : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&auto=format&fit=crop&q=80';
                    $qtdFotos = count($imovel['imagens_array'] ?? []);
                ?>
                <div data-id="<?= $imovel['id'] ?>"
                     x-show="!search || '<?= strtolower(addslashes(esc($imovel['titulo'] . ' ' . ($imovel['endereco'] ?? '') . ' ' . ($imovel['descricao'] ?? '')))) ?>'.includes(search.toLowerCase())"
                     class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs hover:shadow-md transition-all flex flex-col group">
                    
                    <!-- Imagem de Capa -->
                    <div class="relative h-52 bg-slate-100 overflow-hidden">
                        <img src="<?= esc($coverImage) ?>" alt="<?= esc($imovel['titulo']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-slate-900/30"></div>
                        
                        <!-- Topo da Imagem: Badge de Etapa + Botão Drag Handle -->
                        <div class="absolute top-3 left-3 flex items-center gap-2">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-white/95 backdrop-blur-md text-slate-800 shadow-xs capitalize">
                                <?= esc($tabs[$imovel['status']] ?? $imovel['status']) ?>
                            </span>

                            <?php if ($qtdFotos > 1): ?>
                                <button type="button" 
                                        @click="galeriaImovel = { titulo: '<?= addslashes(esc($imovel['titulo'])) ?>', imagens: <?= htmlspecialchars(json_encode($imovel['imagens_array']), ENT_QUOTES, 'UTF-8') ?> }; modalGaleria = true"
                                        class="px-2 py-0.5 text-[11px] font-semibold rounded-md bg-slate-900/70 text-white backdrop-blur-xs hover:bg-slate-900 flex items-center gap-1 shadow-xs" title="Ver todas as fotos">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span><?= $qtdFotos ?> fotos</span>
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Botão de Vídeo e Alça de Arraste (Drag Handle) -->
                        <div class="absolute top-3 right-3 flex items-center gap-1.5">
                            <?php if (!empty($imovel['video'])): ?>
                                <a href="<?= esc($imovel['video']) ?>" target="_blank" 
                                   class="p-1.5 rounded-lg bg-rose-600/90 text-white hover:bg-rose-600 shadow-xs transition-colors" title="Assistir Vídeo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </a>
                            <?php endif; ?>

                            <div class="drag-handle cursor-grab active:cursor-grabbing p-1.5 rounded-lg bg-slate-900/60 backdrop-blur-xs text-white hover:bg-slate-900 shadow-xs transition-colors" title="Segure e arraste para reordenar a prioridade">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                            </div>
                        </div>

                        <!-- Rodapé da Imagem com Endereço -->
                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <p class="text-xs font-medium text-slate-200 flex items-center gap-1.5 truncate">
                                <svg class="w-3.5 h-3.5 text-sky-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <?= esc($imovel['endereco'] ?: 'Endereço não informado') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Conteúdo do Card -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-1 mb-1">
                                <?= esc($imovel['titulo']) ?>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                <?= esc($imovel['descricao'] ?: 'Sem descrição detalhada adicionada.') ?>
                            </p>
                        </div>

                        <!-- Rodapé do Card: Mover Status + Ações -->
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                            
                            <!-- Formulário de Avanço Rápido de Status -->
                            <form action="<?= base_url('imoveis/status/' . $imovel['id']) ?>" method="POST" class="flex-1">
                                <select name="status" onchange="this.form.submit()" 
                                        class="w-full text-xs font-semibold py-1.5 px-2.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 hover:border-brand-500 focus:ring-1 focus:ring-brand-500 cursor-pointer">
                                    <option value="" disabled selected>Mover etapa...</option>
                                    <?php foreach ($tabs as $k => $l): ?>
                                        <option value="<?= esc($k) ?>" <?= $imovel['status'] === $k ? 'disabled' : '' ?>>
                                            &rarr; <?= esc($l) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>

                            <!-- Botões de Edição e Exclusão -->
                            <div class="flex items-center gap-1">
                                <button type="button" 
                                        @click="imovelEdit = {
                                            id: '<?= $imovel['id'] ?>',
                                            titulo: '<?= addslashes(esc($imovel['titulo'])) ?>',
                                            endereco: '<?= addslashes(esc($imovel['endereco'])) ?>',
                                            descricao: '<?= addslashes(esc($imovel['descricao'])) ?>',
                                            status: '<?= $imovel['status'] ?>',
                                            video: '<?= addslashes(esc($imovel['video'] ?? '')) ?>'
                                        }; modalEditar = true"
                                        class="p-1.5 text-slate-400 hover:text-slate-700 rounded-md hover:bg-slate-100" title="Editar Imóvel">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                
                                <a href="<?= base_url('imoveis/delete/' . $imovel['id']) ?>" 
                                   onclick="return confirm('Deseja realmente excluir este imóvel da esteira?')"
                                   class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md hover:bg-rose-50" title="Excluir">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Toast flutuante de Reordenação Salva -->
    <div x-show="orderSavedToast" x-transition.opacity.duration.300ms 
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900 text-white shadow-2xl border border-slate-700 text-xs font-semibold">
        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span>Ordem dos imóveis salva com sucesso!</span>
    </div>

    <!-- MODAL: Novo Imóvel -->
    <div x-show="modalNovo" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="modalNovo" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

            <div x-show="modalNovo" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form action="<?= base_url('imoveis') ?>" method="POST" enctype="multipart/form-data">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Cadastrar Novo Imóvel</h3>
                            <button type="button" @click="modalNovo = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Título do Imóvel *</label>
                                <input type="text" name="titulo" required placeholder="Ex: Apartamento 3 quartos vista mar" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Endereço</label>
                                <input type="text" name="endereco" placeholder="Ex: Rua Moreira César, Icaraí - Niterói/RJ" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Etapa Inicial</label>
                                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                        <?php foreach ($tabs as $k => $l): ?>
                                            <option value="<?= esc($k) ?>" <?= $activeTab === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Link do Vídeo / Tour</label>
                                    <input type="url" name="video" placeholder="https://youtube.com/..." 
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Descrição</label>
                                <textarea name="descricao" rows="3" placeholder="Informações relevantes sobre a captação..." 
                                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden"></textarea>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Fotos do Imóvel (Upload)</label>
                                <input type="file" name="fotos[]" multiple accept="image/*" 
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                <p class="text-xs text-slate-400 mt-1">Ou informe uma URL direta de imagem:</p>
                                <input type="url" name="imagem_url" placeholder="https://images.unsplash.com/..." 
                                       class="w-full mt-1 px-3 py-2 text-xs rounded-lg border border-slate-200">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="modalNovo = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs shadow-xs">
                            Salvar Imóvel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Editar Imóvel -->
    <div x-show="modalEditar" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="modalEditar" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

            <div x-show="modalEditar" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form :action="'<?= base_url('imoveis/update/') ?>' + imovelEdit.id" method="POST" enctype="multipart/form-data">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Editar Imóvel</h3>
                            <button type="button" @click="modalEditar = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Título do Imóvel *</label>
                                <input type="text" name="titulo" x-model="imovelEdit.titulo" required 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Endereço</label>
                                <input type="text" name="endereco" x-model="imovelEdit.endereco" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Etapa Atual</label>
                                    <select name="status" x-model="imovelEdit.status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                        <?php foreach ($tabs as $k => $l): ?>
                                            <option value="<?= esc($k) ?>"><?= esc($l) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Link do Vídeo / Tour</label>
                                    <input type="url" name="video" x-model="imovelEdit.video" 
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Descrição</label>
                                <textarea name="descricao" x-model="imovelEdit.descricao" rows="3" 
                                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden"></textarea>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Adicionar Mais Fotos</label>
                                <input type="file" name="fotos[]" multiple accept="image/*" 
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="modalEditar = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs shadow-xs">
                            Atualizar Imóvel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Galeria de Fotos -->
    <div x-show="modalGaleria" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="modalGaleria" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-md"></div>

            <div x-show="modalGaleria" x-transition class="inline-block align-bottom bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-800">
                <div class="p-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                        <h3 class="text-base font-bold text-white truncate" x-text="galeriaImovel.titulo"></h3>
                        <button type="button" @click="modalGaleria = false" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="grid grid-cols-2 gap-3 max-h-96 overflow-y-auto pr-1">
                        <template x-for="(foto, index) in galeriaImovel.imagens" :key="index">
                            <a :href="foto" target="_blank" class="rounded-xl overflow-hidden bg-slate-800 h-40 block group relative">
                                <img :src="foto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-slate-950/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold">
                                    Abrir em tela cheia &nearr;
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
                <div class="bg-slate-950/60 px-6 py-3 flex justify-end">
                    <button type="button" @click="modalGaleria = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-200 text-xs font-semibold hover:bg-slate-700">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
