<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Plateforme Fiches Agents')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        orange: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="h-full flex flex-col font-sans antialiased bg-slate-50 text-slate-800 min-h-screen">

    <!-- Top Accent Bar (Vert / Jaune / Rouge - Couleurs du Sénégal) -->
    <div class="h-1.5 w-full bg-gradient-to-r from-emerald-600 via-amber-400 to-rose-600 z-50 sticky top-0"></div>

    <div class="flex flex-1 min-h-screen">

        <!-- Sidebar Administration -->
        <aside id="sidebar" class="w-64 bg-white border-r border-slate-200/90 flex flex-col justify-between shrink-0 shadow-xs fixed lg:sticky top-1.5 h-[calc(100vh-6px)] z-40 transition-transform duration-300 -translate-x-full lg:translate-x-0">
            <div>
                <!-- Official Senegal Administration Logo & Header -->
                <div class="p-5 border-b border-slate-100 flex flex-col items-center text-center">
                    <a href="{{ route('admin.agents.index') }}" class="group flex flex-col items-center">
                        <img src="{{ asset('images/MFPremove.png') }}" alt="Logo Officiel Ministère Fonction Publique Sénégal" class="h-16 w-auto object-contain transition-transform group-hover:scale-105 duration-200">
                        <h2 class="font-extrabold text-sm text-slate-900 tracking-tight mt-2">
                            RÉPUBLIQUE DU SÉNÉGAL
                        </h2>
                        <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Ministère de la Fonction Publique</p>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5">
                    <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-2">Menu Principal</p>

                    <!-- Agents List -->
                    <a href="{{ route('admin.agents.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.agents.index') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="p-1.5 rounded-lg {{ request()->routeIs('admin.agents.index') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid fa-users text-xs"></i>
                        </span>
                        <span>Répertoire Agents</span>
                    </a>

                    <!-- New Agent -->
                    <a href="{{ route('admin.agents.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.agents.create') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="p-1.5 rounded-lg {{ request()->routeIs('admin.agents.create') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid fa-user-plus text-xs"></i>
                        </span>
                        <span>Ajouter un Agent</span>
                    </a>

                    <!-- Historique des cartes -->
                    <a href="{{ route('admin.historique.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.historique.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="p-1.5 rounded-lg {{ request()->routeIs('admin.historique.*') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                        </span>
                        <span>Historique des cartes</span>
                    </a>

                    <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mt-6 mb-2">Importations</p>

                    <!-- Import Excel -->
                    <a href="{{ route('admin.agents.show-import-excel') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.agents.show-import-excel') ? 'bg-orange-50 text-orange-800 border border-orange-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="p-1.5 rounded-lg {{ request()->routeIs('admin.agents.show-import-excel') ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-600' }}">
                            <i class="fa-solid fa-file-excel text-xs"></i>
                        </span>
                        <span>Importation Excel / CSV</span>
                    </a>

                    <!-- Import ZIP Photos -->
                    <a href="{{ route('admin.agents.show-import-zip') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 {{ request()->routeIs('admin.agents.show-import-zip') ? 'bg-amber-50 text-amber-900 border border-amber-200 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        <span class="p-1.5 rounded-lg {{ request()->routeIs('admin.agents.show-import-zip') ? 'bg-amber-500 text-slate-950' : 'bg-amber-100 text-amber-700' }}">
                            <i class="fa-solid fa-file-archive text-xs"></i>
                        </span>
                        <span>Importation Photos (ZIP)</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-100 text-center">
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-[11px] text-slate-500">
                    <p class="font-bold text-slate-800">Plateforme Digitale Agent</p>
                    <p class="text-[10px] text-emerald-700 font-semibold mt-0.5">Système Officiel Sécurisé</p>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Bar (Mobile Toggle & Quick Brand) -->
            <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex items-center justify-between shadow-xs sticky top-1.5 z-30">
                <div class="flex items-center space-x-3">
                    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="lg:hidden p-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-sm">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-sm text-slate-900">Gestion des Fiches Agents</span>
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 hidden sm:inline-block">Officiel</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @auth
                        <div class="hidden sm:flex items-center space-x-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
                            <i class="fa-solid fa-user-shield text-emerald-600 text-xs"></i>
                            <span class="text-xs font-bold text-slate-700">{{ Auth::user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold transition-all flex items-center gap-1.5 shadow-xs" title="Déconnexion">
                                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <!-- Content Container -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 w-full">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="text-sm font-semibold">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-700/60 hover:text-emerald-900">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                <i class="fa-solid fa-exclamation"></i>
                            </div>
                            <p class="text-sm font-semibold">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-700/60 hover:text-rose-900">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 bg-white py-4 px-6 text-center text-xs text-slate-500">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                    <span class="font-medium text-slate-600">République du Sénégal &bull; Ministère de la Fonction Publique</span>
                    <p>&copy; {{ date('Y') }} Tous droits réservés.</p>
                </div>
            </footer>
        </div>

    </div>

    <!-- QR Code Generator Library Client-side -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    @stack('scripts')
</body>
</html>
