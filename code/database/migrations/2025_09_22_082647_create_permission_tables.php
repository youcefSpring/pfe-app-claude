<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

public function up(): void
{
    // $tableNames = config('permission.table_names');
    // $columnNames = config('permission.column_names');
    // $pivotRole = config('permission.column_names.role_pivot_key');
    // $pivotPermission = config('permission.column_names.permission_pivot_key');
    // $teams = config('permission.teams');

    // // --- Permissions Table ---
    // if (!Schema::hasTable($tableNames['permissions'])) {
    //     Schema::create($tableNames['permissions'], function (Blueprint $table) {
    //         $table->bigIncrements('id');
    //         $table->string('name');
    //         $table->string('guard_name');
    //         $table->timestamps();
    //         $table->unique(['name', 'guard_name']);
    //     });
    // } else {
    //     Schema::table($tableNames['permissions'], function (Blueprint $table) {
    //         if (!Schema::hasColumn($table->getTable(), 'name')) {
    //             $table->string('name');
    //         }
    //         if (!Schema::hasColumn($table->getTable(), 'guard_name')) {
    //             $table->string('guard_name');
    //         }
    //         if (!Schema::hasColumn($table->getTable(), 'created_at')) {
    //             $table->timestamps();
    //         }
    //     });
    // }

    // // --- Roles Table ---
    // if (!Schema::hasTable($tableNames['roles'])) {
    //     Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
    //         $table->bigIncrements('id');
    //         if ($teams || config('permission.testing')) {
    //             $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
    //             $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
    //         }
    //         $table->string('name');
    //         $table->string('guard_name');
    //         $table->timestamps();

    //         if ($teams || config('permission.testing')) {
    //             $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
    //         } else {
    //             $table->unique(['name', 'guard_name']);
    //         }
    //     });
    // } else {
    //     Schema::table($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
    //         if ($teams && !Schema::hasColumn($table->getTable(), $columnNames['team_foreign_key'])) {
    //             $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable()->after('id');
    //             $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
    //         }
    //         if (!Schema::hasColumn($table->getTable(), 'name')) {
    //             $table->string('name');
    //         }
    //         if (!Schema::hasColumn($table->getTable(), 'guard_name')) {
    //             $table->string('guard_name');
    //         }
    //         if (!Schema::hasColumn($table->getTable(), 'created_at')) {
    //             $table->timestamps();
    //         }
    //     });
    // }

    // // --- Pivot Tables ---
    // // if (!Schema::hasTable($tableNames['model_has_permissions'])) {
    // //     Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
    // //         $table->unsignedBigInteger($pivotPermission);
    // //         $table->string('model_type');
    // //         $table->unsignedBigInteger($columnNames['model_morph_key']);
    // //         $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');
    // //         $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->onDelete('cascade');

    // //         if ($teams) {
    // //             $table->unsignedBigInteger($columnNames['team_foreign_key']);
    // //             $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');
    // //             $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_permission_model_type_primary');
    // //         } else {
    // //             $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_permission_model_type_primary');
    // //         }
    // //     });
    // // }

    // if (!Schema::hasTable($tableNames['model_has_roles'])) {
    //     Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
    //         $table->unsignedBigInteger($pivotRole);
    //         $table->string('model_type');
    //         $table->unsignedBigInteger($columnNames['model_morph_key']);
    //         $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');
    //         $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->onDelete('cascade');

    //         if ($teams) {
    //             $table->unsignedBigInteger($columnNames['team_foreign_key']);
    //             $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');
    //             $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'], 'model_has_roles_role_model_type_primary');
    //         } else {
    //             $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'], 'model_has_roles_role_model_type_primary');
    //         }
    //     });
    // }

    // if (!Schema::hasTable($tableNames['role_has_permissions'])) {
    //     Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
    //         $table->unsignedBigInteger($pivotPermission);
    //         $table->unsignedBigInteger($pivotRole);
    //         $table->foreign($pivotPermission)->references('id')->on($tableNames['permissions'])->onDelete('cascade');
    //         $table->foreign($pivotRole)->references('id')->on($tableNames['roles'])->onDelete('cascade');
    //         $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
    //     });
    // }

    // --- Clear cache ---
    // app('cache')->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
    //     ->forget(config('permission.cache.key'));
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
};
