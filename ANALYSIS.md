# Rapport d'analyse — projet gestion-ci

## Présentation

SaaS de gestion comptable/facturation ciblant PME ivoiriennes et cabinets comptables. Multi-tenant, conformité OHADA/DGI CI, modules Facturation, TVA, Comptabilité, Abonnements.

## Stack technique

-   Backend : **Laravel** (composer.json indique `laravel/framework ^12`) — PHP ^8.2
-   UI : **Livewire** / **Volt**, **Filament** (admin)
-   Frontend tooling : **Vite**, **Tailwind**, **Alpine.js**
-   Base de données : PostgreSQL (prévu) ; tests utilisent SQLite en mémoire
-   Cache/Queue : Redis (prévu)
-   Multitenancy : `spatie/laravel-multitenancy` (domain-based)
-   Auth / RBAC : `laravel/fortify` + `spatie/laravel-permission`
-   Utilitaires : DOMPDF, Intervention Image, Maatwebsite Excel, Spatie Backup, Activity Log

## Structure principale

-   `app/Models` : modèles métiers riches (`Invoice`, `Tenant`, `Company`, `Customer`, `AccountingEntry`, ...)
-   `app/Services` : logique métier découplée (ex. `AccountingService`, `TaxService`)
-   `app/Providers` : `VoltServiceProvider` configure Volt, `AppServiceProvider` vide (espace pour bindings)
-   `config/multitenancy.php` : configuration domain-based; queues tenant-aware
-   Tests : `phpunit.xml` avec env `DB_DATABASE=:memory:` — facilitant CI

## Points forts

-   Bonne séparation domain/service (Services + Models avec scopes et méthodes métier).
-   Multitenancy pris en charge via Spatie, ce qui facilite scalabilité SaaS.
-   Configuration de tests prête (sqlite in-memory) pour exécution rapide en CI.
-   Documentation produit et roadmap présentes (`README.md`, `ARCHITECTURE.md`, `ROADMAP.md`).

## Risques et recommandations rapides

-   Versions : docs mentionnent Laravel 11 / PHP 8.3, mais `composer.json` exige Laravel ^12 et PHP ^8.2 — aligner la doc et la configuration.
-   Sécurité/dépendances : plusieurs paquets tiers (spatie, filament, dompdf, excel). Exécuter audit vulnérabilités et mettre à jour les packages critiques.
-   Multitenancy : vérifier isolation des données (policies, scopes, tests d'isolement) et tâches de backup/restauration tenant-aware.
-   Gestion des numéros factures : logique dans `Company::getNextInvoiceNumber()` — vérifier concurrence (utiliser transaction/lock si forte charge).
-   TVA : `TaxService::calculateVAT()` arrondit à l'unité — confirmer règle fiscale et couverture des cas (produits à taux réduit, exonérations).

## Commandes conseillées (env local Windows / WSL)

```bash
# installation minimale
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build

# lancer tests
php artisan test

# vérifier dépendances
composer outdated
# audit (si disponible dans votre environnement)
composer audit || echo "composer audit non disponible"
```

## Suggestions d'actions prioritaires

-   1. Aligner versions & doc (PHP / Laravel).
-   2. Lancer `composer outdated` + `composer audit`, corriger vulnérabilités critiques.
-   3. Exécuter la suite de tests et corriger erreurs.
-   4. Écrire tests d'isolement multitenant (migrations, requests, jobs).
-   5. Revue rapide des endpoints publics et politiques (`spatie/permission`).

---

Fichier créé : `ANALYSIS.md`.
