<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckQcPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // 1. Get the current route prefix or segment that identifies the form module
        // Example path: qc-sistem/bahan-baku-roasting/create -> segment(2) is bahan-baku-roasting
        $module = $request->segment(2);

        // Bypass permission check for Berat Produk module
        // - GET create is needed for navigation from other modules
        // - POST store is needed so saving data is not blocked by permission checks
        if ($module === 'berat-produk') {
            if ($request->method() === 'GET' && $request->segment(3) === 'create') {
                return $next($request);
            }

            if ($request->method() === 'POST') {
                return $next($request);
            }
        }

        // Allow AJAX requests bypassing the strict structure Check
        if ($module === 'ajax' || $request->ajax() || $module === 'get-emulsi-by-produk') {
            return $next($request);
        }

        if ($user && $module) {
            // Determine action based on HTTP method and path
            $action = 'view'; // Default is view (index, show)
            
            $method = $request->method();
            $segment3 = $request->segment(3);
            $segment4 = $request->segment(4); // e.g. /qc-sistem/module/{id}/edit
            
            if ($method === 'POST') {
                $action = 'create'; // store
            } elseif (in_array($method, ['PUT', 'PATCH'])) {
                $action = 'edit'; // update
            } elseif ($method === 'DELETE') {
                $action = 'delete'; // destroy
            } elseif ($method === 'GET') {
                if ($segment3 === 'create') {
                    $action = 'create';
                } elseif ($segment4 === 'edit') {
                    $action = 'edit';
                }
            }

            // Pengecualian nama module untuk URL yang berbeda dengan nama Permission
            $moduleMap = [
                'penggorengan' => 'proses-penggorengan',
                 'berat-produk'=>'berat_produk_bag_box',
            ];
            
            $permissionModule = $moduleMap[$module] ?? $module;

            // Determine permission string based on CRUD
            $permissionRequired = $action . '-' . $permissionModule;
            
            // Terapkan perlindungan HANYA untuk aksi Create, Edit, dan Delete (Action != view)
            // Sehingga user bebas 'Melihat' halaman, namun tombol aksinya akan diatur lewat Blade
            if ($action !== 'view' && !$user->hasPermissionTo($permissionRequired)) {
                
                // Jika request adalah JSON/AJAX, kembalikan JSON 403
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Anda tidak memiliki hak akses untuk melakukan aksi ini.'], 403);
                }
                
                // Teruskan kemana dia berasal atau ke dashboard
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.');
            }
        }
        
        return $next($request);
    }
}
