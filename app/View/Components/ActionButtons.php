<?php
namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ActionButtons extends Component
{
    public $item;
    public $routePrefix;
    public $permissionPrefix;
    public $showHistory;
    public $showView;
    
    public function __construct($item, $routePrefix, $permissionPrefix = null, $showHistory = true, $showView = true)
    {
        $this->item = $item;
        $this->routePrefix = $routePrefix;
        $this->permissionPrefix = $permissionPrefix ?: $routePrefix;
        $this->showHistory = $showHistory;
        $this->showView = $showView;
    }

    public function canAccess()
    {
        // View, Edit, or Delete permissions allow access to this action-buttons group at least
        return Auth::check() && (
            Auth::user()->hasPermissionTo('view-' . $this->permissionPrefix) ||
            Auth::user()->hasPermissionTo('edit-' . $this->permissionPrefix) ||
            Auth::user()->hasPermissionTo('delete-' . $this->permissionPrefix)
        );
    }

    public function isViewOnlyRole()
    {
        return Auth::check() && 
               Auth::user()->hasPermissionTo('view-' . $this->permissionPrefix) && 
               !Auth::user()->hasPermissionTo('edit-' . $this->permissionPrefix) && 
               !Auth::user()->hasPermissionTo('delete-' . $this->permissionPrefix);
    }

    public function hasRoute($routeName)
    {
        return Route::has($routeName);
    }

    public function render()
    {
        return view('components.action-buttons');
    }
}