<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions based on route names
        $permissions = [
            // Dashboard permissions
            'dashboard.index',
            
            // URL management permissions
            'urls.index',
            'urls.create',
            'urls.store',
            'urls.show',
            'urls.edit',
            'urls.update',
            'urls.destroy',
            
            // Analytics permissions
            'analytics.index',
            'analytics.show',
            
            // Admin permissions
            'admin.dashboard',
            'admin.users.index',
            'admin.users.create',
            'admin.users.store',
            'admin.users.show',
            'admin.users.edit',
            'admin.users.update',
            'admin.users.destroy',
            'admin.analytics.index',
            'admin.analytics.show',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Assign permissions to roles
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());
        
        // User gets limited permissions
        $userRole->givePermissionTo([
            'dashboard.index',
            'urls.index',
            'urls.create',
            'urls.store',
            'urls.show',
            'urls.edit',
            'urls.update',
            'urls.destroy',
            'analytics.index',
            'analytics.show',
        ]);

        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $adminUser->assignRole('admin');

        // Create test user
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $testUser->assignRole('user');
    }
}
