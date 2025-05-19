<?php

namespace App\View\Components;
use App\Models\Tenant;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $tenants = Tenant::with('domains')->get();

        return view('tenants.index', ['tenants' => $tenants]);
    }
}
