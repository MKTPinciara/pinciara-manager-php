<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="{ 
    modalNova: false, 
    modalEditar: false, 
    placaEdit: { id: '', titulo: '', largura: '', altura: '', material: '', tipo: '', quantidade: 1, valor: '', observacao: '', status: '', data_envio: '' } 
}">

    <!-- Cabeçalho -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Controle de Placas e Impressos</h1>
            <p class="text-sm text-slate-500 mt-1">Gerencie a confecção gráfica, controle de estoque e custos financeiros com fornecedores.</p>
        </div>
        <div>
            <button @click="modalNova = true" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm shadow-md shadow-brand-600/20 transition-all transform active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nova Placa
            </button>
        </div>
    </div>

    <!-- Indicadores Financeiros e de Estoque (KPIs) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Investido</span>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">
                R$ <?= number_format($totals['total_gasto'] ?? 0, 2, ',', '.') ?>
            </div>
            <span class="text-[11px] text-emerald-600 font-semibold mt-1 inline-block">Custo acumulado em placas</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Volume Total</span>
            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">
                <?= $totals['total_placas'] ?? 0 ?> <span class="text-sm font-normal text-slate-500">unidades</span>
            </div>
            <span class="text-[11px] text-slate-500 mt-1 inline-block">Em todas as categorias</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Em Produção</span>
            <div class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-1">
                <?= $counts['produzir'] ?? 0 ?> <span class="text-sm font-normal text-slate-500">pedidos</span>
            </div>
            <span class="text-[11px] text-amber-600 font-semibold mt-1 inline-block">Aguardando confecção</span>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Prontas em Estoque</span>
            <div class="text-xl sm:text-2xl font-extrabold text-sky-600 mt-1">
                <?= $counts['disponíveis'] ?? 0 ?> <span class="text-sm font-normal text-slate-500">lotes</span>
            </div>
            <span class="text-[11px] text-sky-600 font-semibold mt-1 inline-block">Disponíveis para corretores</span>
        </div>
    </div>

    <!-- Navegação de Abas -->
    <div class="border-b border-slate-200 mb-8 overflow-x-auto">
        <nav class="flex space-x-2 sm:space-x-4 min-w-max pb-px" aria-label="Abas de Placas">
            <?php foreach ($tabs as $key => $label): ?>
                <?php 
                    $isActive = ($activeTab === $key);
                    $count = $counts[$key] ?? 0;
                ?>
                <a href="<?= base_url('placas?tab=' . urlencode($key)) ?>" 
                   class="flex items-center gap-2 py-3 px-4 border-b-2 font-semibold text-sm transition-all whitespace-nowrap <?= $isActive ? 'border-brand-600 text-brand-700 bg-brand-50/50 rounded-t-lg' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?>">
                    <span><?= esc($label) ?></span>
                    <span class="px-2 py-0.5 text-xs rounded-full font-bold <?= $isActive ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-600' ?>">
                        <?= $count ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Lista de Placas -->
    <?php if (empty($placas)): ?>
        <div class="text-center py-16 px-4 bg-white border border-slate-200 rounded-2xl shadow-xs">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Nenhuma placa nesta categoria</h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto mt-1">Não há nenhum registro com o status "<?= esc($tabs[$activeTab] ?? $activeTab) ?>".</p>
            <button @click="modalNova = true" class="mt-4 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                + Adicionar Placa
            </button>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($placas as $placa): ?>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Topo do Card -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700">
                                <?= esc($placa['tipo']) ?>
                            </span>
                            <span class="text-sm font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                R$ <?= number_format($placa['valor'] ?? 0, 2, ',', '.') ?>
                            </span>
                        </div>

                        <!-- Título -->
                        <h3 class="text-base font-bold text-slate-900 mb-2">
                            <?= esc($placa['titulo']) ?>
                        </h3>

                        <!-- Grid de Especificações Técnicas -->
                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 mb-4">
                            <div>
                                <span class="text-slate-400 block">Medidas:</span>
                                <strong class="text-slate-800"><?= number_format($placa['largura'], 2) ?> x <?= number_format($placa['altura'], 2) ?> m</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Material:</span>
                                <strong class="text-slate-800"><?= esc($placa['material']) ?></strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Quantidade:</span>
                                <strong class="text-slate-800"><?= $placa['quantidade'] ?> un.</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Previsão/Envio:</span>
                                <strong class="text-slate-800"><?= $placa['data_envio'] ? date('d/m/Y', strtotime($placa['data_envio'])) : 'Não informada' ?></strong>
                            </div>
                        </div>

                        <?php if (!empty($placa['observacao'])): ?>
                            <p class="text-xs text-slate-500 mb-4 italic line-clamp-2">
                                "<?= esc($placa['observacao']) ?>"
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Rodapé: Avanço de Status & Ações -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        
                        <!-- Avanço Contextual de Status -->
                        <form action="<?= base_url('placas/status/' . $placa['id']) ?>" method="POST" class="flex-1">
                            <?php if ($placa['status'] === 'produzir'): ?>
                                <input type="hidden" name="status" value="pagar">
                                <button type="submit" class="w-full text-xs font-semibold py-2 px-3 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition-colors">
                                    Enviar para Pagar &rarr;
                                </button>
                            <?php elseif ($placa['status'] === 'pagar'): ?>
                                <input type="hidden" name="status" value="pago">
                                <button type="submit" class="w-full text-xs font-semibold py-2 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition-colors">
                                    Confirmar Pago &rarr;
                                </button>
                            <?php elseif ($placa['status'] === 'pago'): ?>
                                <input type="hidden" name="status" value="disponíveis">
                                <button type="submit" class="w-full text-xs font-semibold py-2 px-3 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 transition-colors">
                                    Mover p/ Disponíveis &rarr;
                                </button>
                            <?php elseif ($placa['status'] === 'disponíveis'): ?>
                                <input type="hidden" name="status" value="usadas">
                                <button type="submit" class="w-full text-xs font-semibold py-2 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 transition-colors">
                                    Marcar como Usada &rarr;
                                </button>
                            <?php else: ?>
                                <span class="text-xs text-slate-400 font-medium italic block text-center py-1.5">Concluída</span>
                            <?php endif; ?>
                        </form>

                        <!-- Botões de Ação -->
                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    @click="placaEdit = {
                                        id: '<?= $placa['id'] ?>',
                                        titulo: '<?= addslashes(esc($placa['titulo'])) ?>',
                                        largura: '<?= $placa['largura'] ?>',
                                        altura: '<?= $placa['altura'] ?>',
                                        material: '<?= addslashes(esc($placa['material'])) ?>',
                                        tipo: '<?= addslashes(esc($placa['tipo'])) ?>',
                                        quantidade: '<?= $placa['quantidade'] ?>',
                                        valor: '<?= $placa['valor'] ?>',
                                        observacao: '<?= addslashes(esc($placa['observacao'] ?? '')) ?>',
                                        status: '<?= $placa['status'] ?>',
                                        data_envio: '<?= $placa['data_envio'] ?? '' ?>'
                                    }; modalEditar = true"
                                    class="p-1.5 text-slate-400 hover:text-slate-700 rounded-md hover:bg-slate-100" title="Editar Placa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            
                            <a href="<?= base_url('placas/delete/' . $placa['id']) ?>" 
                               onclick="return confirm('Deseja realmente remover esta placa?')"
                               class="p-1.5 text-slate-400 hover:text-rose-600 rounded-md hover:bg-rose-50" title="Excluir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- MODAL: Nova Placa -->
    <div x-show="modalNova" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="modalNova" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

            <div x-show="modalNova" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form action="<?= base_url('placas') ?>" method="POST">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Cadastrar Nova Placa</h3>
                            <button type="button" @click="modalNova = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Título / Identificação *</label>
                                <input type="text" name="titulo" required placeholder="Ex: Placa Vende-se - Icaraí" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Largura (m)</label>
                                    <input type="number" step="0.01" name="largura" value="1.00" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Altura (m)</label>
                                    <input type="number" step="0.01" name="altura" value="0.70" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Material</label>
                                    <select name="material" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                        <option value="Polionda">Polionda</option>
                                        <option value="Lona com Reforço">Lona com Reforço</option>
                                        <option value="PVC 2mm">PVC 2mm</option>
                                        <option value="ACM">ACM (Alumínio)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Tipo</label>
                                    <select name="tipo" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                        <option value="Vende-se">Vende-se</option>
                                        <option value="Aluga-se">Aluga-se</option>
                                        <option value="Exclusividade">Exclusividade</option>
                                        <option value="Lançamento">Lançamento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Quantidade</label>
                                    <input type="number" name="quantidade" value="1" min="1" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Valor Total (R$)</label>
                                    <input type="number" step="0.01" name="valor" value="0.00" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Data Envio</label>
                                    <input type="date" name="data_envio" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status Inicial</label>
                                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                    <?php foreach ($tabs as $k => $l): ?>
                                        <option value="<?= esc($k) ?>" <?= $activeTab === $k ? 'selected' : '' ?>><?= esc($l) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Observações</label>
                                <textarea name="observacao" rows="2" placeholder="Informações de acabamento, telefone para impressão, etc." 
                                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="modalNova = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs shadow-xs">
                            Salvar Placa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: Editar Placa -->
    <div x-show="modalEditar" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div x-show="modalEditar" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

            <div x-show="modalEditar" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <form :action="'<?= base_url('placas/update/') ?>' + placaEdit.id" method="POST">
                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <h3 class="text-lg font-bold text-slate-900">Editar Placa</h3>
                            <button type="button" @click="modalEditar = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Título / Identificação *</label>
                                <input type="text" name="titulo" x-model="placaEdit.titulo" required 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Largura (m)</label>
                                    <input type="number" step="0.01" name="largura" x-model="placaEdit.largura" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Altura (m)</label>
                                    <input type="number" step="0.01" name="altura" x-model="placaEdit.altura" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Material</label>
                                    <select name="material" x-model="placaEdit.material" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                        <option value="Polionda">Polionda</option>
                                        <option value="Lona com Reforço">Lona com Reforço</option>
                                        <option value="PVC 2mm">PVC 2mm</option>
                                        <option value="ACM">ACM (Alumínio)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Tipo</label>
                                    <select name="tipo" x-model="placaEdit.tipo" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                        <option value="Vende-se">Vende-se</option>
                                        <option value="Aluga-se">Aluga-se</option>
                                        <option value="Exclusividade">Exclusividade</option>
                                        <option value="Lançamento">Lançamento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Quantidade</label>
                                    <input type="number" name="quantidade" x-model="placaEdit.quantidade" min="1" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Valor Total (R$)</label>
                                    <input type="number" step="0.01" name="valor" x-model="placaEdit.valor" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Data Envio</label>
                                    <input type="date" name="data_envio" x-model="placaEdit.data_envio" 
                                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status Atual</label>
                                <select name="status" x-model="placaEdit.status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden">
                                    <?php foreach ($tabs as $k => $l): ?>
                                        <option value="<?= esc($k) ?>"><?= esc($l) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Observações</label>
                                <textarea name="observacao" x-model="placaEdit.observacao" rows="2" 
                                          class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-brand-500 focus:outline-hidden"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="modalEditar = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-semibold text-xs shadow-xs">
                            Atualizar Placa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
