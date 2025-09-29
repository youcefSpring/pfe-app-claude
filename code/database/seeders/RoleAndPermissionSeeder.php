<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cache before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Subject permissions
            'subjects.view',
            'subjects.create',
            'subjects.edit',
            'subjects.delete',
            'subjects.validate',
            'subjects.publish',

            // Team permissions
            'teams.view',
            'teams.create',
            'teams.edit',
            'teams.delete',
            'teams.validate',
            'teams.manage_members',

            // Project permissions
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'projects.assign',
            'projects.supervise',

            // Deliverable permissions
            'deliverables.view',
            'deliverables.upload',
            'deliverables.review',
            'deliverables.download',

            // Defense permissions
            'defenses.view',
            'defenses.schedule',
            'defenses.manage',
            'defenses.grade',
            'defenses.generate_pv',

            // User management permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage_roles',

            // System permissions
            'system.configure',
            'system.reports',
            'system.backup',
            'rooms.manage',
            'conflicts.resolve',

            // Dashboard permissions
            'dashboard.admin',
            'dashboard.teacher',
            'dashboard.student',

            // Notification permissions
            'notifications.send',
            'notifications.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createStudentRole();
        $this->createTeacherRole();
        $this->createChefMasterRole();
        $this->createAdminPfeRole();
        $this->createSuperAdminRole();
    }

    private function createStudentRole()
    {
        $role = Role::create(['name' => 'student']);

        $permissions = [
            'subjects.view',
            'teams.view',
            'teams.create',
            'teams.edit',
            'teams.manage_members',
            'projects.view',
            'deliverables.view',
            'deliverables.upload',
            'deliverables.download',
            'defenses.view',
            'dashboard.student',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createTeacherRole()
    {
        $role = Role::create(['name' => 'teacher']);

        $permissions = [
            'subjects.view',
            'subjects.create',
            'subjects.edit',
            'subjects.delete',
            'teams.view',
            'projects.view',
            'projects.supervise',
            'deliverables.view',
            'deliverables.review',
            'deliverables.download',
            'defenses.view',
            'defenses.grade',
            'dashboard.teacher',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createChefMasterRole()
    {
        $role = Role::create(['name' => 'chef_master']);

        $permissions = [
            'subjects.view',
            'subjects.validate',
            'subjects.publish',
            'teams.view',
            'teams.validate',
            'projects.view',
            'projects.assign',
            'deliverables.view',
            'deliverables.download',
            'defenses.view',
            'defenses.schedule',
            'defenses.manage',
            'defenses.grade',
            'conflicts.resolve',
            'system.reports',
            'dashboard.admin',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createAdminPfeRole()
    {
        $role = Role::create(['name' => 'admin_pfe']);

        $permissions = [
            'subjects.view',
            'subjects.validate',
            'subjects.publish',
            'teams.view',
            'teams.validate',
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'projects.assign',
            'deliverables.view',
            'deliverables.download',
            'defenses.view',
            'defenses.schedule',
            'defenses.manage',
            'defenses.generate_pv',
            'users.view',
            'users.create',
            'users.edit',
            'users.manage_roles',
            'system.configure',
            'system.reports',
            'rooms.manage',
            'conflicts.resolve',
            'notifications.send',
            'notifications.manage',
            'dashboard.admin',
        ];

        $role->givePermissionTo($permissions);
    }

    private function createSuperAdminRole()
    {
        $role = Role::create(['name' => 'super_admin']);

        // Give all permissions to super admin
        $role->givePermissionTo(Permission::all());
    }
}