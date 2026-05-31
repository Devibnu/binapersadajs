<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'inquiry-quotation.view' => 'Lihat inquiry quotation',
            'inquiry-quotation.create' => 'Tambah inquiry quotation',
            'inquiry-quotation.edit' => 'Ubah inquiry quotation',
            'inquiry-quotation.delete' => 'Hapus inquiry quotation',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::query()->updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'group_name' => 'Inquiry & Quotation',
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
            ->whereIn('slug', [
                'inquiry-quotation.view',
                'inquiry-quotation.create',
                'inquiry-quotation.edit',
                'inquiry-quotation.delete',
            ])
            ->delete();
    }
};
