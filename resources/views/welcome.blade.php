<!DOCTYPE html>
<html class="light" lang="fr">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>MiniCRM - Gérez vos relations clients efficacement</title>
    <meta name="description" content="MiniCRM est une solution CRM moderne et intuitive pour gérer vos contacts, opportunités et tâches commerciales." />
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet" />
    
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": {
                            50: "#f0fdf4",
                            100: "#dcfce7",
                            200: "#bbf7d0",
                            300: "#86efac",
                            400: "#4ade80",
                            500: "#22c55e",
                            600: "#16a34a",
                            700: "#15803d",
                            800: "#166534",
                            900: "#14532d",
                        },
                        "accent": {
                            50: "#eef2ff",
                            100: "#e0e7ff",
                            200: "#c7d2fe",
                            300: "#a5b4fc",
                            400: "#818cf8",
                            500: "#6366f1",
                            600: "#4f46e5",
                            700: "#4338ca",
                            800: "#3730a3",
                            900: "#312e81",
                        }
                    },
                    fontFamily: {
                        "sans": ["Inter", "system-ui", "sans-serif"]
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                    },
                },
            },
        }
    </script>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #22c55e 0%, #4f46e5 50%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #f0fdf4 0%, #eef2ff 50%, #faf5ff 100%);
        }
        
        .dark .hero-gradient {
            background: linear-gradient(135deg, #0c1f0c 0%, #1e1b4b 50%, #1f1544 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .glow {
            box-shadow: 0 0 60px rgba(34, 197, 94, 0.3);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
        }
        
        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        /* Scroll animations */
        [data-animate] {
            opacity: 0;
            transform: translateY(30px);
        }
        
        [data-animate].visible {
            animation: slideUp 0.6s ease-out forwards;
        }
        
        /* Gradient border */
        .gradient-border {
            position: relative;
        }
        
        .gradient-border::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 2px;
            background: linear-gradient(135deg, #22c55e, #4f46e5);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }
    </style>
</head>

<body class="bg-white dark:bg-zinc-950 font-sans text-zinc-800 dark:text-zinc-100 antialiased overflow-x-hidden">
    
    <!-- Navbar -->
    <header class="fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between h-16 md:h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-primary-500/30 transition-shadow">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white dark:border-zinc-950"></div>
                    </div>
                    <span class="text-xl font-bold tracking-tight">Mini<span class="text-primary-600">CRM</span></span>
                </a>
                
                <!-- Nav Links -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Fonctionnalités</a>
                    <a href="#modules" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Modules</a>
                    <a href="#stats" class="text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Statistiques</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-accent-600 rounded-xl hover:from-primary-700 hover:to-accent-700 transition-all shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30 hover:-translate-y-0.5">
                                <span class="material-symbols-outlined mr-2 text-lg">dashboard</span>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="hidden sm:inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-600 to-accent-600 rounded-xl hover:from-primary-700 hover:to-accent-700 transition-all shadow-lg shadow-primary-500/25 hover:shadow-xl hover:shadow-primary-500/30 hover:-translate-y-0.5">
                                    Commencer gratuitement
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="relative min-h-screen hero-gradient overflow-hidden">
            <!-- Background decorations -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-20 left-10 w-72 h-72 bg-primary-400/20 rounded-full blur-3xl animate-pulse-slow"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent-400/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-primary-300/10 to-accent-300/10 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20 md:pt-40 md:pb-32">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full text-sm font-medium mb-6 animate-fade-in">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-600"></span>
                            </span>
                            Solution CRM complète
                        </div>
                        
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black leading-tight mb-6 animate-slide-up">
                            Gérez vos 
                            <span class="gradient-text">relations clients</span>
                            avec simplicité
                        </h1>
                        
                        <p class="text-lg sm:text-xl text-zinc-600 dark:text-zinc-400 mb-8 max-w-xl mx-auto lg:mx-0 animate-slide-up" style="animation-delay: 0.1s;">
                            MiniCRM vous aide à suivre vos contacts, gérer vos opportunités commerciales, 
                            et optimiser votre productivité avec une interface moderne et intuitive.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start animate-slide-up" style="animation-delay: 0.2s;">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-gradient-to-r from-primary-600 to-accent-600 rounded-2xl hover:from-primary-700 hover:to-accent-700 transition-all shadow-xl shadow-primary-500/25 hover:shadow-2xl hover:shadow-primary-500/30 hover:-translate-y-1 group">
                                    Démarrer maintenant
                                    <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            @endif
                            <a href="#features" 
                               class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl hover:border-primary-300 dark:hover:border-primary-700 transition-all hover:shadow-lg">
                                <span class="material-symbols-outlined mr-2">play_circle</span>
                                Découvrir
                            </a>
                        </div>
                        
                        <!-- Trust indicators -->
                        <div class="mt-12 flex items-center gap-6 justify-center lg:justify-start animate-slide-up" style="animation-delay: 0.3s;">
                            <div class="flex -space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-white text-xs font-bold">JD</div>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-white text-xs font-bold">MK</div>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-white text-xs font-bold">AS</div>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-white text-xs font-bold">+</div>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Utilisé par votre équipe</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">Gérez efficacement vos relations</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Visual - Dashboard Preview -->
                    <div class="relative animate-fade-in" style="animation-delay: 0.4s;">
                        <div class="relative z-10">
                            <!-- Main card -->
                            <div class="glass-card rounded-3xl p-6 shadow-2xl glow">
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div class="text-xs text-zinc-400 font-mono">minicrm.local/dashboard</div>
                                </div>
                                
                                <!-- Stats preview -->
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 rounded-2xl p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white text-lg">people</span>
                                            </div>
                                            <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Contacts</span>
                                        </div>
                                        <div class="text-2xl font-bold text-blue-900 dark:text-blue-100">247</div>
                                    </div>
                                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/20 rounded-2xl p-4">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white text-lg">trending_up</span>
                                            </div>
                                            <span class="text-xs font-medium text-green-700 dark:text-green-300">Pipeline</span>
                                        </div>
                                        <div class="text-2xl font-bold text-green-900 dark:text-green-100">1.2M DA</div>
                                    </div>
                                </div>
                                
                                <!-- Activity preview -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">AB</div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Nouvelle opportunité créée</div>
                                            <div class="text-xs text-zinc-500">Ahmed B. - Il y a 2 min</div>
                                        </div>
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">+50K DA</span>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                        <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white text-sm font-bold">SK</div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Réunion planifiée avec client</div>
                                            <div class="text-xs text-zinc-500">Sara K. - Il y a 15 min</div>
                                        </div>
                                        <span class="material-symbols-outlined text-blue-500">event</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Floating cards -->
                            <div class="absolute -top-6 -right-6 glass-card rounded-2xl p-4 shadow-xl animate-float hidden lg:block">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white">check_circle</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">12 tâches</div>
                                        <div class="text-xs text-zinc-500">complétées aujourd'hui</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="absolute -bottom-4 -left-6 glass-card rounded-2xl p-4 shadow-xl animate-float hidden lg:block" style="animation-delay: 3s;">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-accent-400 to-accent-500 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-white">chat</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">8 interactions</div>
                                        <div class="text-xs text-zinc-500">ce matin</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce-slow">
                <a href="#features" class="flex flex-col items-center text-zinc-400 hover:text-primary-600 transition-colors">
                    <span class="text-xs font-medium mb-2">Découvrir les fonctionnalités</span>
                    <span class="material-symbols-outlined">keyboard_arrow_down</span>
                </a>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
                    <span class="inline-block px-4 py-2 bg-accent-100 dark:bg-accent-900/30 text-accent-700 dark:text-accent-300 rounded-full text-sm font-medium mb-4">
                        Fonctionnalités
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                        Tout ce dont vous avez besoin pour 
                        <span class="gradient-text">réussir</span>
                    </h2>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400">
                        Des outils puissants et intuitifs pour gérer efficacement votre activité commerciale.
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="feature-card group p-8 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-xl" data-animate>
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-white text-2xl">contacts</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-zinc-900 dark:text-zinc-100">Gestion des Contacts</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Centralisez toutes les informations de vos contacts et accédez à leur historique complet en un clic.
                        </p>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="feature-card group p-8 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-xl" data-animate>
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-white text-2xl">trending_up</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-zinc-900 dark:text-zinc-100">Pipeline Commercial</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Visualisez et gérez vos opportunités commerciales à travers un pipeline intuitif et personnalisable.
                        </p>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="feature-card group p-8 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-xl" data-animate>
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-white text-2xl">task_alt</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-zinc-900 dark:text-zinc-100">Gestion des Tâches</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Planifiez, assignez et suivez vos tâches pour ne jamais manquer une échéance importante.
                        </p>
                    </div>
                    
                    <!-- Feature 4 -->
                    <div class="feature-card group p-8 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-xl" data-animate>
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-white text-2xl">forum</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-zinc-900 dark:text-zinc-100">Suivi des Interactions</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Enregistrez chaque interaction avec vos clients : appels, emails, réunions et notes.
                        </p>
                    </div>
                    
                    <!-- Feature 5 -->
                    <div class="feature-card group p-8 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-xl" data-animate>
                        <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-white text-2xl">inventory_2</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-zinc-900 dark:text-zinc-100">Catalogue Produits</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Gérez votre catalogue de produits et services avec suivi de stock et catégorisation.
                        </p>
                    </div>
                    
                    <!-- Feature 6 -->
                    <div class="feature-card group p-8 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-xl" data-animate>
                        <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-white text-2xl">chat</span>
                        </div>
                        <h3 class="text-xl font-bold mb-3 text-zinc-900 dark:text-zinc-100">Messagerie Interne</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">
                            Communiquez avec votre équipe via une messagerie intégrée pour une collaboration efficace.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modules Section -->
        <section id="modules" class="py-24 bg-gradient-to-b from-zinc-50 to-white dark:from-zinc-900 dark:to-zinc-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
                    <span class="inline-block px-4 py-2 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full text-sm font-medium mb-4">
                        Modules
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                        Une solution <span class="gradient-text">complète</span>
                    </h2>
                    <p class="text-lg text-zinc-600 dark:text-zinc-400">
                        Découvrez tous les modules disponibles dans MiniCRM pour optimiser votre gestion commerciale.
                    </p>
                </div>
                
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Module Cards -->
                    @php
                        $modules = [
                            ['icon' => 'dashboard', 'name' => 'Dashboard', 'desc' => 'Vue d\'ensemble', 'color' => 'from-indigo-500 to-purple-600'],
                            ['icon' => 'people', 'name' => 'Contacts', 'desc' => 'Gestion clients', 'color' => 'from-blue-500 to-cyan-600'],
                            ['icon' => 'monetization_on', 'name' => 'Opportunités', 'desc' => 'Pipeline ventes', 'color' => 'from-green-500 to-emerald-600'],
                            ['icon' => 'checklist', 'name' => 'Tâches', 'desc' => 'To-do list', 'color' => 'from-orange-500 to-amber-600'],
                            ['icon' => 'history', 'name' => 'Interactions', 'desc' => 'Historique', 'color' => 'from-purple-500 to-pink-600'],
                            ['icon' => 'inventory', 'name' => 'Produits', 'desc' => 'Catalogue', 'color' => 'from-rose-500 to-red-600'],
                            ['icon' => 'mail', 'name' => 'Messages', 'desc' => 'Communication', 'color' => 'from-teal-500 to-cyan-600'],
                            ['icon' => 'admin_panel_settings', 'name' => 'Admin', 'desc' => 'Rôles & droits', 'color' => 'from-slate-500 to-zinc-600'],
                        ];
                    @endphp
                    
                    @foreach($modules as $module)
                        <div class="group relative p-6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-transparent transition-all duration-300 hover:shadow-xl overflow-hidden" data-animate>
                            <div class="absolute inset-0 bg-gradient-to-br {{ $module['color'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="w-12 h-12 bg-gradient-to-br {{ $module['color'] }} rounded-xl flex items-center justify-center mb-4 group-hover:bg-white/20 transition-colors">
                                    <span class="material-symbols-outlined text-white text-xl">{{ $module['icon'] }}</span>
                                </div>
                                <h3 class="font-bold text-zinc-900 dark:text-zinc-100 group-hover:text-white transition-colors">{{ $module['name'] }}</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400 group-hover:text-white/80 transition-colors">{{ $module['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="stats" class="py-24 bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900 text-white relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-accent-500/10 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16" data-animate>
                    <span class="inline-block px-4 py-2 bg-white/10 text-white rounded-full text-sm font-medium mb-4 backdrop-blur-sm">
                        Performances
                    </span>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                        Conçu pour la <span class="gradient-text">performance</span>
                    </h2>
                    <p class="text-lg text-zinc-400">
                        Une plateforme fiable et rapide pour accompagner votre croissance.
                    </p>
                </div>
                
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="stat-card text-center" data-animate>
                        <div class="stat-icon w-16 h-16 mx-auto bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-4 transition-transform">
                            <span class="material-symbols-outlined text-white text-3xl">speed</span>
                        </div>
                        <div class="text-4xl font-black mb-2">100%</div>
                        <div class="text-zinc-400">Temps de disponibilité</div>
                    </div>
                    
                    <div class="stat-card text-center" data-animate>
                        <div class="stat-icon w-16 h-16 mx-auto bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center mb-4 transition-transform">
                            <span class="material-symbols-outlined text-white text-3xl">bolt</span>
                        </div>
                        <div class="text-4xl font-black mb-2">&lt;100ms</div>
                        <div class="text-zinc-400">Temps de réponse</div>
                    </div>
                    
                    <div class="stat-card text-center" data-animate>
                        <div class="stat-icon w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-4 transition-transform">
                            <span class="material-symbols-outlined text-white text-3xl">security</span>
                        </div>
                        <div class="text-4xl font-black mb-2">100%</div>
                        <div class="text-zinc-400">Données sécurisées</div>
                    </div>
                    
                    <div class="stat-card text-center" data-animate>
                        <div class="stat-icon w-16 h-16 mx-auto bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-4 transition-transform">
                            <span class="material-symbols-outlined text-white text-3xl">devices</span>
                        </div>
                        <div class="text-4xl font-black mb-2">24/7</div>
                        <div class="text-zinc-400">Accès multiplateforme</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 bg-white dark:bg-zinc-950">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-600 via-accent-600 to-purple-700 p-12 md:p-16 text-center text-white" data-animate>
                    <!-- Background decoration -->
                    <div class="absolute inset-0 overflow-hidden pointer-events-none">
                        <div class="absolute -top-24 -right-24 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6">
                            Prêt à transformer votre gestion client ?
                        </h2>
                        <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
                            Rejoignez MiniCRM dès maintenant et découvrez comment simplifier 
                            la gestion de vos relations clients.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" 
                                   class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-primary-700 bg-white rounded-2xl hover:bg-zinc-100 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1">
                                    Créer un compte gratuit
                                    <svg class="ml-2 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold text-white bg-white/20 rounded-2xl hover:bg-white/30 transition-all backdrop-blur-sm border border-white/30">
                                    Se connecter
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <a href="/" class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-accent-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Mini<span class="text-primary-600">CRM</span></span>
                    </a>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-6 max-w-sm">
                        Solution CRM moderne et intuitive pour gérer efficacement vos relations clients et développer votre activité.
                    </p>
                </div>
                
                <!-- Links -->
                <div>
                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Produit</h4>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Fonctionnalités</a></li>
                        <li><a href="#modules" class="text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Modules</a></li>
                        <li><a href="#stats" class="text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Performances</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Compte</h4>
                    <ul class="space-y-3">
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Connexion</a></li>
                        @endif
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="text-zinc-600 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Inscription</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-zinc-200 dark:border-zinc-800 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    © {{ date('Y') }} MiniCRM. Tous droits réservés.
                </p>
                <div class="flex items-center gap-4">
                    <span class="text-xs text-zinc-400">Fait avec ❤️ en Laravel</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll Animation Script -->
    <script>
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        let lastScroll = 0;
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 50) {
                navbar.classList.add('bg-white/90', 'dark:bg-zinc-900/90', 'backdrop-blur-lg', 'shadow-lg');
            } else {
                navbar.classList.remove('bg-white/90', 'dark:bg-zinc-900/90', 'backdrop-blur-lg', 'shadow-lg');
            }
            
            lastScroll = currentScroll;
        });
        
        // Scroll animations
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>
