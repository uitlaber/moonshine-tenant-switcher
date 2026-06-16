<?php

namespace Uitlaber\MoonShineTenantSwitcher\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Uitlaber\MoonShineTenantSwitcher\TenantManager;

class SwitchTenantController extends Controller
{
    public function __invoke(Request $request, TenantManager $tenants): RedirectResponse
    {
        $id = $request->input('tenant');

        if ($tenants->isValidId($id)) {
            $tenants->setCurrent($id);
        }

        return redirect()->back();
    }
}
