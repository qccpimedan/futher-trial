<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class AccessControlController extends Controller
{
    /**
     * Display a listing of roles and their permissions for qc-sistem modules
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('role', 'asc')->get();
        $selectedRole = null;
        $modules = $this->getQcModules();
        $rolePermissions = [];

        if ($request->has('role_id')) {
            $selectedRole = Role::find($request->role_id);
            if ($selectedRole) {
                // Get permissions associated directly with this specific Role mapping
                $rolePermissions = $selectedRole->permissions()->pluck('name')->toArray();
            }
        }

        // Make sure all modules have their corresponding permissions exist in DB
        $this->ensurePermissionsExist($modules);

        return view('akses-control.index', compact('roles', 'selectedRole', 'modules', 'rolePermissions'));
    }

    /**
     * Update the role's permissions
     */
    public function update(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($request->role_id);
        $submittedPermissions = $request->permissions ?? [];
        
        $permissionIds = [];
        foreach ($submittedPermissions as $permName) {
            $permission = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            $permissionIds[] = $permission->id;
        }

        // Sync local mapping
        $role->permissions()->sync($permissionIds);

        return redirect()->back()->with('success', 'Akses kontrol untuk role ' . $role->role . ' berhasil diperbarui!');
    }

    /**
     * Get all dynamically available directories inside resources/views/qc-sistem
     */
    private function getQcModules()
    {
        $path = resource_path('views/qc-sistem');
        $modules = [];

        if (File::exists($path)) {
            $directories = File::directories($path);
            foreach ($directories as $dir) {
                $dirName = basename($dir);
                // Convert directory name to readable format (e.g. bahan_baku_roasting -> Bahan Baku Roasting)
                $label = ucwords(str_replace(['_', '-'], ' ', $dirName));
                
                // standardkan format permission module menjadi pakai dash (-)
                $moduleKebab = str_replace('_', '-', $dirName);

                $modules[] = [
                    'id' => $dirName,   // Form slug internal
                    'label' => $label,             // Viewable name
                    'permissions' => [
                        'view' => 'view-' . $moduleKebab,
                        'create' => 'create-' . $moduleKebab,
                        'edit' => 'edit-' . $moduleKebab,
                        'delete' => 'delete-' . $moduleKebab,
                    ]
                ];
            }
        }

        // Sort alphabetically to maintain layout order
        usort($modules, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });

        return $modules;
    }

    /**
     * Seed any new permissions on layout load
     */
    private function ensurePermissionsExist($modules)
    {
        foreach ($modules as $module) {
            foreach ($module['permissions'] as $action => $permName) {
                Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
            }
        }
    }
}
