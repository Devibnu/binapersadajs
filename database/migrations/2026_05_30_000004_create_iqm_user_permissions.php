<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'iqm-user.view' => 'Lihat user portal IQM',
            'iqm-user.create' => 'Tambah user portal IQM',
            'iqm-user.edit' => 'Ubah user portal IQM',
            'iqm-user.delete' => 'Hapus user portal IQM',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'group_name' => 'IQM',
            ]);
        }

        Role::query()
            ->where('is_super_admin', true)
            ->get()
            ->each(function (Role $role) use ($permissions) {
                $role->permissions()->syncWithoutDetaching(
                    Permission::query()->whereIn('slug', array_keys($permissions))->pluck('id')
                );
            });
    }

    public function down(): void
    {
        Permission::query()
            ->whereIn('slug', ['iqm-user.view', 'iqm-user.create', 'iqm-user.edit', 'iqm-user.delete'])
            ->delete();
    }
};
