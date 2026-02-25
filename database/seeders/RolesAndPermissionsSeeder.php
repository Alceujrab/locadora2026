<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions by module
        $permissions = [
            // Usuarios
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Veiculos
            'vehicles.view', 'vehicles.create', 'vehicles.edit', 'vehicles.delete',
            // Clientes
            'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
            // Reservas
            'reservations.view', 'reservations.create', 'reservations.edit', 'reservations.delete',
            // Contratos
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.delete', 'contracts.sign',
            // Faturas
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            // Financeiro
            'financial.view', 'financial.manage',
            // OS
            'service_orders.view', 'service_orders.create', 'service_orders.edit', 'service_orders.delete',
            // Configuracoes
            'settings.view', 'settings.edit',
            // Relatorios
            'reports.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $gerente = Role::firstOrCreate(['name' => 'gerente']);
        $gerente->syncPermissions([
            'users.view', 'vehicles.view', 'vehicles.create', 'vehicles.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'reservations.view', 'reservations.create', 'reservations.edit',
            'contracts.view', 'contracts.create', 'contracts.edit', 'contracts.sign',
            'invoices.view', 'invoices.create', 'invoices.edit',
            'financial.view', 'financial.manage',
            'service_orders.view', 'service_orders.create', 'service_orders.edit',
            'reports.view',
        ]);

        $operador = Role::firstOrCreate(['name' => 'operador']);
        $operador->syncPermissions([
            'vehicles.view', 'customers.view', 'customers.create',
            'reservations.view', 'reservations.create', 'reservations.edit',
            'contracts.view', 'contracts.create',
            'service_orders.view', 'service_orders.create',
        ]);

        $financeiro = Role::firstOrCreate(['name' => 'financeiro']);
        $financeiro->syncPermissions([
            'invoices.view', 'invoices.create', 'invoices.edit',
            'financial.view', 'financial.manage',
            'customers.view', 'contracts.view',
            'reports.view',
        ]);

        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        // Clientes não têm permissões no painel admin
    }
}
