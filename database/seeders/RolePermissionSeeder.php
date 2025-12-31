<?php
// database/seeders/RolePermissionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        echo "🔐 Création des permissions...\n";

        // Créer toutes les permissions
        $permissions = [
            // Factures
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',
            'send_invoices',
            'export_invoices',

            // Devis
            'view_quotes',
            'create_quotes',
            'edit_quotes',
            'delete_quotes',
            'send_quotes',
            'convert_quotes',

            // Clients
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            'import_customers',
            'export_customers',

            // Fournisseurs
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // Produits
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'import_products',
            'manage_stock',

            // Paiements
            'view_payments',
            'create_payments',
            'edit_payments',
            'delete_payments',

            // Comptabilité
            'view_accounting',
            'create_entries',
            'edit_entries',
            'delete_entries',
            'post_entries',
            'view_reports',
            'export_reports',

            // TVA & Fiscalité
            'view_tax_declarations',
            'create_tax_declarations',
            'edit_tax_declarations',
            'submit_tax_declarations',

            // Dashboard & Statistiques
            'view_dashboard',
            'view_statistics',

            // Paramètres
            'manage_company_settings',
            'manage_users',
            'manage_roles',
            'manage_subscription',
            'view_activity_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        echo "✅ " . count($permissions) . " permissions créées\n\n";

        echo "👥 Création des rôles...\n";

        // ==================== RÔLE : OWNER ====================
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $owner->givePermissionTo(Permission::all());
        echo "✅ Rôle 'Owner' créé (toutes les permissions)\n";

        // ==================== RÔLE : ADMIN ====================
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $adminPermissions = [
            'view_invoices', 'create_invoices', 'edit_invoices', 'delete_invoices', 'send_invoices', 'export_invoices',
            'view_quotes', 'create_quotes', 'edit_quotes', 'delete_quotes', 'send_quotes', 'convert_quotes',
            'view_customers', 'create_customers', 'edit_customers', 'delete_customers', 'import_customers', 'export_customers',
            'view_suppliers', 'create_suppliers', 'edit_suppliers', 'delete_suppliers',
            'view_products', 'create_products', 'edit_products', 'delete_products', 'import_products', 'manage_stock',
            'view_payments', 'create_payments', 'edit_payments', 'delete_payments',
            'view_accounting', 'create_entries', 'edit_entries', 'post_entries', 'view_reports', 'export_reports',
            'view_tax_declarations', 'create_tax_declarations', 'edit_tax_declarations', 'submit_tax_declarations',
            'view_dashboard', 'view_statistics',
            'manage_company_settings', 'manage_users', 'view_activity_logs',
        ];
        $admin->givePermissionTo($adminPermissions);
        echo "✅ Rôle 'Admin' créé (" . count($adminPermissions) . " permissions)\n";

        // ==================== RÔLE : ACCOUNTANT ====================
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountantPermissions = [
            'view_invoices', 'create_invoices', 'edit_invoices', 'send_invoices', 'export_invoices',
            'view_quotes', 'create_quotes', 'edit_quotes',
            'view_customers', 'create_customers', 'edit_customers',
            'view_suppliers', 'create_suppliers', 'edit_suppliers',
            'view_products',
            'view_payments', 'create_payments', 'edit_payments',
            'view_accounting', 'create_entries', 'edit_entries', 'post_entries', 'view_reports', 'export_reports',
            'view_tax_declarations', 'create_tax_declarations', 'edit_tax_declarations',
            'view_dashboard', 'view_statistics',
        ];
        $accountant->givePermissionTo($accountantPermissions);
        echo "✅ Rôle 'Accountant' créé (" . count($accountantPermissions) . " permissions)\n";

        // ==================== RÔLE : SALES ====================
        $sales = Role::firstOrCreate(['name' => 'sales']);
        $salesPermissions = [
            'view_invoices', 'create_invoices', 'edit_invoices', 'send_invoices',
            'view_quotes', 'create_quotes', 'edit_quotes', 'send_quotes', 'convert_quotes',
            'view_customers', 'create_customers', 'edit_customers',
            'view_products',
            'view_dashboard',
        ];
        $sales->givePermissionTo($salesPermissions);
        echo "✅ Rôle 'Sales' créé (" . count($salesPermissions) . " permissions)\n";

        // ==================== RÔLE : VIEWER ====================
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewerPermissions = [
            'view_invoices',
            'view_quotes',
            'view_customers',
            'view_suppliers',
            'view_products',
            'view_payments',
            'view_accounting',
            'view_reports',
            'view_dashboard',
        ];
        $viewer->givePermissionTo($viewerPermissions);
        echo "✅ Rôle 'Viewer' créé (" . count($viewerPermissions) . " permissions)\n";

        echo "\n🎉 Tous les rôles et permissions ont été créés avec succès !\n";
    }
}
