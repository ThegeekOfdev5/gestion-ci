<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        @csrf

        <title>{{ config('app.name') }} - Logiciel de Gestion Intelligent pour PME Ivoiriennes</title>

        <!-- Favicons -->
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta name="theme-color" content="#3b82f6">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .dark .grid-bg {
                background-image:
                    linear-gradient(to right, rgba(30, 41, 59, 0.05) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(30, 41, 59, 0.05) 1px, transparent 1px);
                background-size: 6rem 4rem;
            }
            .grid-bg {
                background-image:
                    linear-gradient(to right, rgba(203, 213, 225, 0.15) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(203, 213, 225, 0.15) 1px, transparent 1px);
                background-size: 6rem 4rem;
            }
            .gradient-text {
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .gradient-bg {
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .dark .glass-card {
                background: rgba(15, 23, 42, 0.7);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .section-title {
                position: relative;
                display: inline-block;
            }
            .section-title::after {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 3px;
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                border-radius: 2px;
            }
        </style>
    </head>
    <body class="relative bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans">
        <!-- Background Grid Pattern -->
        <div class="absolute inset-0 -z-10 h-full w-full bg-white dark:bg-slate-950 grid-bg"></div>

        <!-- Navigation -->
        <nav class="fixed w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ config('app.name') }}</span>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center space-x-8">
                        <a href="#fonctionnalites" class="text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">Fonctionnalités</a>
                        <a href="#tarifs" class="text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">Tarifs</a>
                        <a href="#conformite" class="text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">Conformité</a>
                        <a href="#ressources" class="text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">Ressources</a>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center space-x-4">
                        <!-- Theme Toggle -->
                        <button id="theme-toggle" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                            <svg id="sun-icon" class="w-5 h-5 text-slate-700 dark:text-slate-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <svg id="moon-icon" class="w-5 h-5 text-slate-700 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        </button>

                        <!-- CTA Button -->
                        <a href="#tarifs" class="px-6 py-3 gradient-bg text-white rounded-lg font-semibold hover:shadow-lg transition-all">
                            Essai Gratuit
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="min-h-screen flex items-center justify-center pt-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto text-center">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500/10 to-purple-500/10 dark:from-blue-500/20 dark:to-purple-500/20 rounded-full mb-8">
                    <span class="text-sm font-semibold gradient-text">🇨🇮 Conforme DGI & OHADA</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-8">
                    <span class="block">Logiciel de Gestion</span>
                    <span class="block gradient-text">Intelligent</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-400 mb-12 max-w-3xl mx-auto">
                    Pour les PME Ivoiriennes. Tout-en-un, abordable et 100% conforme.
                    <span class="block font-semibold text-blue-600 dark:text-blue-400 mt-2">
                        10-20x moins cher que les solutions internationales
                    </span>
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                    <a href="#tarifs" class="px-8 py-4 gradient-bg text-white text-lg font-semibold rounded-xl hover:shadow-xl transition-all duration-300">
                        Commencer l'essai gratuit
                    </a>
                    <a href="#demo" class="px-8 py-4 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-lg font-semibold rounded-xl border border-slate-300 dark:border-slate-700 hover:border-blue-500 transition-colors">
                        Voir la démo
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto">
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="text-3xl font-bold gradient-text mb-2">90%</div>
                        <div class="text-slate-600 dark:text-slate-400">Économies</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="text-3xl font-bold gradient-text mb-2">100%</div>
                        <div class="text-slate-600 dark:text-slate-400">Conformité</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="text-3xl font-bold gradient-text mb-2">24/7</div>
                        <div class="text-slate-600 dark:text-slate-400">Support</div>
                    </div>
                    <div class="glass-card p-6 rounded-2xl">
                        <div class="text-3xl font-bold gradient-text mb-2">+500</div>
                        <div class="text-slate-600 dark:text-slate-400">Clients</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trusted By Section -->
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4">
                        En association avec des institutions Ivoiriennes
                    </p>
                </div>

                <!-- Logos Grid -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-8 items-center justify-center opacity-70">
                    <div class="flex flex-col items-center justify-center p-4">
                        <div class="w-24 h-16 bg-gradient-to-br from-orange-100 to-orange-50 dark:from-orange-900/30 dark:to-orange-800/20 rounded-xl flex items-center justify-center mb-3">
                            <span class="text-2xl font-bold text-orange-600 dark:text-orange-400">DGI</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Fiscalité</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4">
                        <div class="w-24 h-16 bg-gradient-to-br from-green-100 to-green-50 dark:from-green-900/30 dark:to-green-800/20 rounded-xl flex items-center justify-center mb-3">
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">OHADA</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Comptabilité</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4">
                        <div class="w-24 h-16 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/20 rounded-xl flex items-center justify-center mb-3">
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">CNPS</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Social</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4">
                        <div class="w-24 h-16 bg-gradient-to-br from-purple-100 to-purple-50 dark:from-purple-900/30 dark:to-purple-800/20 rounded-xl flex items-center justify-center mb-3">
                            <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">UMOA</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Monétaire</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4">
                        <div class="w-24 h-16 bg-gradient-to-br from-red-100 to-red-50 dark:from-red-900/30 dark:to-red-800/20 rounded-xl flex items-center justify-center mb-3">
                            <span class="text-2xl font-bold text-red-600 dark:text-red-400">CCI</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Commerce</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-4">
                        <div class="w-24 h-16 bg-gradient-to-br from-yellow-100 to-yellow-50 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-xl flex items-center justify-center mb-3">
                            <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">PME</span>
                        </div>
                        <span class="text-sm text-slate-600 dark:text-slate-400">Entreprises</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Value Proposition -->
        <section id="fonctionnalites" class="py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6 section-title">
                        Tout ce dont vous avez besoin pour gérer votre entreprise
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                        Une plateforme unifiée qui combine ERP, CRM et gestion financière spécifiquement conçue pour le marché Ivoirien
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="glass-card p-8 rounded-2xl">
                        <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Comptabilité Automatisée</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Génération automatique des déclarations fiscales (TVA, BIC, IS, IRPP) conformes aux normes DGI.
                        </p>
                    </div>

                    <div class="glass-card p-8 rounded-2xl">
                        <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Facturation Intelligente</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Factures, devis et bons de commande conformes aux normes locales avec TVA 18% intégrée.
                        </p>
                    </div>

                    <div class="glass-card p-8 rounded-2xl">
                        <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Gestion des Relations Clients</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Suivi complet des clients, historique des transactions et gestion des créances.
                        </p>
                    </div>

                    <div class="glass-card p-8 rounded-2xl">
                        <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Gestion des Stocks</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Suivi en temps réel des inventaires, alertes de réapprovisionnement et gestion multi-dépôts.
                        </p>
                    </div>

                    <div class="glass-card p-8 rounded-2xl">
                        <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Paie & RH</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Bulletins de paie conformes CNPS, gestion des congés et suivi des performances.
                        </p>
                    </div>

                    <div class="glass-card p-8 rounded-2xl">
                        <div class="w-14 h-14 gradient-bg rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Intégrations Locales</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Orange Money, MTN Mobile Money, banques locales et autres services Ivoiriens.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="tarifs" class="py-20 bg-slate-50 dark:bg-slate-900/30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6 section-title">
                        Tarifs transparents
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                        Choisissez la formule adaptée à votre entreprise. 30 jours d'essai gratuit, sans engagement.
                    </p>
                </div>

                <!-- Pricing Cards -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Starter -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700">
                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">STARTER</h3>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-4xl font-bold">Gratuit</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400">ou 2 500 FCFA/mois</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>10 factures/mois</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Gestion contacts</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Tableau de bord simple</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="block w-full py-3 text-center font-semibold bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-white rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                            Commencer
                        </a>
                    </div>

                    <!-- Essentiel -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border-2 border-blue-500 dark:border-blue-400 relative transform scale-105">
                        <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-1 rounded-full text-sm font-semibold">
                                Plus populaire
                            </span>
                        </div>

                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">ESSENTIEL</h3>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-4xl font-bold">12 000</span>
                                <span class="text-slate-600 dark:text-slate-400 ml-2">FCFA/mois</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400">~18€ par mois</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Comptabilité complète</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Facturation illimitée</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Déclarations TVA auto</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="block w-full py-3 text-center font-semibold gradient-bg text-white rounded-lg hover:shadow-lg transition-all">
                            Essayer gratuitement
                        </a>
                    </div>

                    <!-- Business -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700">
                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">BUSINESS</h3>
                            <div class="flex items-center justify-center mb-2">
                                <span class="text-4xl font-bold">25 000</span>
                                <span class="text-slate-600 dark:text-slate-400 ml-2">FCFA/mois</span>
                            </div>
                            <p class="text-slate-600 dark:text-slate-400">~38€ par mois</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Tout Essentiel +</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Gestion commerciale</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Intégrations bancaires</span>
                            </li>
                        </ul>

                        <a href="{{ route('register') }}" class="block w-full py-3 text-center font-semibold bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                            Essayer gratuitement
                        </a>
                    </div>

                    <!-- Cabinet -->
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8">
                        <div class="text-center mb-8">
                            <h3 class="text-xl font-bold text-white mb-2">CABINET</h3>
                            <div class="flex flex-col items-center justify-center mb-2">
                                <div class="text-4xl font-bold text-white">50 000</div>
                                <div class="text-slate-300">FCFA/mois + 5 000/client</div>
                            </div>
                            <p class="text-slate-400">~76€ + 8€/client</p>
                        </div>

                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center text-slate-200">
                                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Gestion multi-dossiers</span>
                            </li>
                            <li class="flex items-center text-slate-200">
                                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Portail client</span>
                            </li>
                            <li class="flex items-center text-slate-200">
                                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Outils collaboratifs</span>
                            </li>
                        </ul>

                        <a href="#" class="block w-full py-3 text-center font-semibold bg-white text-slate-900 rounded-lg hover:bg-slate-100 transition-colors">
                            Contacter les ventes
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Conformity Section -->
        <section id="conformite" class="py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6 section-title">
                        Conforme aux normes Ivoiriennes
                    </h2>
                    <p class="text-xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                        Développé spécifiquement pour répondre aux exigences légales et fiscales de la Côte d'Ivoire
                    </p>
                </div>

                <!-- Compliance Cards -->
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20">
                        <div class="text-5xl mb-6">📋</div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Normes DGI</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Déclarations fiscales automatisées selon les dernières réglementations
                        </p>
                    </div>

                    <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20">
                        <div class="text-5xl mb-6">📊</div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">SYSCOHADA</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Comptabilité conforme aux normes OHADA en vigueur
                        </p>
                    </div>

                    <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20">
                        <div class="text-5xl mb-6">👥</div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">CNPS & Social</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Paie et déclarations sociales conformes aux barèmes CNPS
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-20 bg-gradient-to-r from-blue-600 to-blue-800">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
                    Prêt à transformer votre gestion d'entreprise ?
                </h2>
                <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
                    Rejoignez les centaines d'entreprises Ivoiriennes qui font confiance à {{ config('app.name') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-blue-600 text-lg font-semibold rounded-xl hover:shadow-xl transition-all">
                        Essai gratuit 30 jours
                    </a>
                    <a href="tel:+2250700000000" class="px-8 py-4 bg-transparent border-2 border-white text-white text-lg font-semibold rounded-xl hover:bg-white/10 transition-colors">
                        📞 Nous contacter
                    </a>
                </div>
                <p class="text-sm text-blue-200 mt-6">
                    Aucune carte bancaire requise • Annulation à tout moment • Support local inclus
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 gradient-bg rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold">Gest<span class="gradient-text">CI</span></span>
                        </div>
                        <p class="text-slate-400 text-sm">
                            Logiciel de gestion tout-en-un conçu pour les PME Ivoiriennes
                        </p>
                    </div>

                    <div>
                        <h4 class="font-bold text-lg mb-6">Produit</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Fonctionnalités</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Tarifs</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Conformité</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">API</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-lg mb-6">Entreprise</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">À propos</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Blog</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Carrières</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Presse</a></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-lg mb-6">Support</h4>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Centre d'aide</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Contact</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Statut</a></li>
                            <li><a href="#" class="text-slate-400 hover:text-white transition-colors">Formations</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-800 pt-8 text-center">
                    <p class="text-slate-400 text-sm">
                        © {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés. Made with ❤️ in Côte d'Ivoire
                    </p>
                </div>
            </div>
        </footer>

        <script>
            // Theme Toggle
            const themeToggle = document.getElementById('theme-toggle');
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');
            const html = document.documentElement;

            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
            const currentTheme = localStorage.getItem('theme');

            if (currentTheme === 'dark' || (!currentTheme && prefersDark.matches)) {
                html.classList.add('dark');
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                html.classList.remove('dark');
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }

            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');

                if (html.classList.contains('dark')) {
                    localStorage.setItem('theme', 'dark');
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                } else {
                    localStorage.setItem('theme', 'light');
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                }
            });

            // Smooth scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
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

            // Animation on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.glass-card').forEach(card => {
                observer.observe(card);
            });
        </script>
    </body>
</html>
