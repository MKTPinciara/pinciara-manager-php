<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pinciara Manager') ?></title>
    <!-- Compiled Tailwind CSS & CDN fallback -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js & SortableJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <!-- Google Fonts: Inter / Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full flex flex-col antialiased text-slate-800" x-data="{ mobileMenuOpen: false }">

    <!-- Top Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <a href="<?= base_url('imoveis') ?>" class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-sky-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20 font-bold text-xl">
                            P
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-slate-900">Pinciara<span class="text-brand-600">Manager</span></span>
                            <span class="hidden sm:inline-block ml-2 px-2 py-0.5 text-[11px] font-semibold tracking-wide uppercase bg-sky-100 text-sky-800 rounded-full">CodeIgniter 4</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="<?= base_url('imoveis') ?>" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 <?= ($activeNav ?? '') === 'imoveis' ? 'bg-brand-50 text-brand-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Esteira de Imóveis
                    </a>
                    <a href="<?= base_url('placas') ?>" 
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 <?= ($activeNav ?? '') === 'placas' ? 'bg-brand-50 text-brand-700 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Controle de Placas
                    </a>
                </nav>

                <!-- User profile & Logout -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-semibold text-slate-800 leading-tight"><?= esc(session()->get('userName') ?? 'Usuário') ?></span>
                        <span class="text-xs text-slate-500"><?= esc(session()->get('userEmail') ?? '') ?></span>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center font-bold text-slate-700 text-sm shadow-xs">
                        <?= strtoupper(substr(session()->get('userName') ?? 'U', 0, 1)) ?>
                    </div>
                    <a href="<?= base_url('logout') ?>" title="Sair do sistema" 
                       class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
            <a href="<?= base_url('imoveis') ?>" class="block px-3 py-2 rounded-md text-base font-semibold <?= ($activeNav ?? '') === 'imoveis' ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-100' ?>">Esteira de Imóveis</a>
            <a href="<?= base_url('placas') ?>" class="block px-3 py-2 rounded-md text-base font-semibold <?= ($activeNav ?? '') === 'placas' ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-100' ?>">Controle de Placas</a>
            <div class="border-t border-slate-100 my-2 pt-2">
                <a href="<?= base_url('logout') ?>" class="block px-3 py-2 rounded-md text-base font-medium text-rose-600 hover:bg-rose-50">Sair da Conta</a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div x-data="{ show: true }" x-show="show" class="mb-5 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs transition-all">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium"><?= session()->getFlashdata('success') ?></span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div x-data="{ show: true }" x-show="show" class="mb-5 flex items-center justify-between p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl shadow-xs transition-all">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-medium"><?= session()->getFlashdata('error') ?></span>
                </div>
                <button @click="show = false" class="text-rose-500 hover:text-rose-800">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Dynamic Content -->
        <?= $this->renderSection('content') ?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>&copy; <?= date('Y') ?> Pinciara Imóveis. Todos os direitos reservados.</span>
            <span class="text-slate-400 font-mono">CodeIgniter 4 + Tailwind CSS + MySQL</span>
        </div>
    </footer>

</body>
</html>
