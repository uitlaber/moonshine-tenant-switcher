<?php

namespace Atlon\MoonShineTenantSwitcher\Http\Controllers;

use Atlon\MoonShineTenantSwitcher\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
