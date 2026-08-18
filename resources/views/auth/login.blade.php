<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Super Admin - Plateforme Fiches Agents</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --emerald-700: #047857;
            --emerald-800: #065f46;
            --emerald-950: #052e16;
            --slate-50: #f8fafc;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                radial-gradient(circle at top right, #ecfdf5, transparent 40%),
                radial-gradient(circle at bottom left, #f8fafc, transparent 40%),
                #f1f5f9;
            padding: 1.25rem;
        }

        .login-card {
            width: 100%;
            max-width: 1050px;
            background: #ffffff;
            border-radius: 1.75rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(5, 46, 22, 0.15), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #d1fae5;
            display: flex;
            flex-direction: row;
        }

        .login-visual {
            flex: 1.1;
            position: relative;
            background: linear-gradient(145deg, #052e16 0%, #064e3b 60%, #047857 100%);
            min-height: 560px;
            display: none;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .login-visual {
                display: flex;
            }
        }

        .login-visual-bg-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.07;
            background-image: radial-gradient(circle, #fff 1.5px, transparent 1.5px);
            background-size: 20px 20px;
            pointer-events: none;
        }

        .login-visual-content {
            position: relative;
            z-index: 10;
            height: 100%;
            padding: 3.5rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #ffffff;
        }

        .login-form-container {
            flex: 1;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        @media (min-width: 992px) {
            .login-form-container {
                padding: 3.5rem 3.5rem;
            }
        }

        .form-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.025em;
            margin-bottom: 0.35rem;
        }

        .form-subtitle {
            color: var(--slate-600);
            font-size: 0.9rem;
            margin-bottom: 1.75rem;
        }

        .form-label {
            display: block;
            font-size: 0.825rem;
            font-weight: 700;
            color: var(--slate-700);
            margin-bottom: 0.45rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            font-size: 0.925rem;
            color: #0f172a;
            background-color: #ffffff;
        }

        .form-control:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12);
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem 1.25rem;
            background: linear-gradient(135deg, #052e16 0%, #064e3b 100%);
            border: 1px solid #d97706;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.95rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(5, 46, 22, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            cursor: pointer;
        }

        .btn-login:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(5, 46, 22, 0.3);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .error-box {
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.18em;
            font-size: 0.725rem;
            font-weight: 800;
            color: #fcd34d;
            margin-bottom: 0.75rem;
            display: inline-block;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--slate-600);
            cursor: pointer;
            user-select: none;
            font-weight: 600;
        }

        .remember-me input {
            width: 1.05rem;
            height: 1.05rem;
            accent-color: #059669;
            cursor: pointer;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        
        <!-- Panneau Visuel Gauche (Masqué sur mobile) -->
        <div class="login-visual">
            <div class="login-visual-bg-pattern"></div>
            
            <div class="login-visual-content">
                <!-- Haut : Logo & Titres -->
                <div>
                    <div class="w-18 h-18 bg-white/95 backdrop-blur-sm rounded-2xl p-3 shadow-lg inline-flex items-center justify-center mb-6 border border-emerald-400/30">
                        <img src="{{ asset('images/MFPremove.png') }}" alt="Logo Ministère" class="h-12 w-auto object-contain">
                    </div>
                    
                    <div class="eyebrow">Plateforme Sécurisée &bull; République du Sénégal</div>
                    <h2 class="text-2xl lg:text-3xl font-extrabold text-white leading-tight tracking-tight">
                        Gestion & Vérification des Fiches Agents
                    </h2>
                    <p class="mt-3.5 text-emerald-100/80 text-sm leading-relaxed max-w-sm">
                        Système centralisé d'administration des fiches d'agents, génération des cartes professionnelles et vérification numérique par QR Code.
                    </p>
                </div>

                <!-- Bas : Badge officiel & Version -->
                <div class="pt-6 border-t border-emerald-700/50 flex items-center justify-between text-xs font-bold text-emerald-200/80">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i> Ministère de la Fonction Publique
                    </span>
                    <span class="text-[11px] bg-black/20 px-2.5 py-1 rounded-md border border-white/10 font-mono">v1.0 Officiel</span>
                </div>
            </div>
        </div>

        <!-- Panneau Formulaire Droit -->
        <div class="login-form-container">
            
            <!-- Logo Mobile uniquement -->
            <div class="text-center mb-6 md:hidden">
                <div class="inline-flex w-16 h-16 bg-white border border-emerald-200 rounded-2xl p-2.5 shadow-sm items-center justify-center">
                    <img src="{{ asset('images/MFPremove.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
            </div>
            
            <div class="text-center md:text-left mb-6">
                <span class="text-[11px] font-extrabold tracking-wider text-emerald-700 uppercase bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200/80 inline-block mb-2">
                    Espace Super Admin
                </span>
                <h1 class="form-title">Connexion</h1>
                <p class="form-subtitle">Accédez à votre espace de gestion administrateur</p>
            </div>

            <!-- Affichage des Erreurs -->
            @if ($errors->any())
                <div class="error-box flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-base flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- Message Succès -->
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold px-4 py-3 rounded-xl text-xs sm:text-sm mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="form-label">Adresse email administrateur</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        value="{{ old('email', 'paulmbaye@admin.com') }}" 
                        required 
                        autofocus 
                        placeholder="admin@fonctionpublique.gouv.sn"
                    >
                </div>

                <div>
                    <label for="password" class="form-label">Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        required 
                        placeholder="••••••••"
                    >
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket text-amber-400"></i>
                    <span>Se connecter</span>
                </button>
            </form>

            <!-- Boîte Compte de Démonstration -->
            <div class="mt-6 p-3.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs text-slate-600 space-y-1.5">
                <div class="font-extrabold text-slate-700 flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-amber-600"></i> Compte administrateur par défaut :
                </div>
                <div class="flex flex-wrap items-center gap-2 font-mono text-[11.5px] pt-0.5">
                    <span class="bg-white px-2.5 py-1 rounded-lg border border-slate-200 font-bold text-slate-800">paulmbaye@admin.com</span>
                    <span class="bg-white px-2.5 py-1 rounded-lg border border-slate-200 font-bold text-slate-800">passer123</span>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>