<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pinciara Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 text-slate-100">

    <div class="max-w-md w-full bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl shadow-sky-500/10">
        
        <!-- Header / Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-600 to-cyan-400 mx-auto flex items-center justify-center text-white font-extrabold text-3xl shadow-lg shadow-sky-500/30 mb-4">
                P
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Pinciara <span class="text-sky-400">Manager</span></h1>
            <p class="text-sm text-slate-400 mt-1">Plataforma interna de gestão imobiliária</p>
            <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700 text-xs text-sky-300 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                CodeIgniter 4 + MySQL
            </div>
        </div>

        <!-- Alertas de Erro / Sucesso -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><?= session()->getFlashdata('success') ?></span>
            </div>
        <?php endif; ?>

        <!-- Form de Login -->
        <form action="<?= base_url('login') ?>" method="POST" id="loginForm" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">E-mail</label>
                <div class="relative">
                    <input type="email" id="email" name="email" required value="<?= old('email') ?>" 
                           placeholder="seu.email@exemplo.com"
                           class="w-full px-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700 text-white placeholder-slate-500 focus:outline-hidden focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Senha</label>
                <input type="password" id="password" name="password" required
                       placeholder="••••••••"
                       class="w-full px-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700 text-white placeholder-slate-500 focus:outline-hidden focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all text-sm">
            </div>

            <button type="submit" 
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-semibold text-sm shadow-lg shadow-sky-500/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2">
                Acessar Plataforma
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Divisor -->
        <div class="relative my-6 text-center">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-800"></div></div>
            <span class="relative bg-slate-900 px-3 text-xs uppercase text-slate-500 font-semibold">ou teste agora</span>
        </div>

        <!-- Botão Rápido de Visitante -->
        <button type="button" onclick="loginAsVisitor()"
                class="w-full py-3 px-4 rounded-xl bg-slate-800/60 hover:bg-slate-800 border border-slate-700 hover:border-slate-600 text-slate-300 hover:text-white text-xs font-semibold transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Entrar como Visitante (1 clique)
        </button>

    </div>

    <script>
        function loginAsVisitor() {
            document.getElementById('email').value = 'visitante@pi.com';
            document.getElementById('password').value = 'visitante';
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>
