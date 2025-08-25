<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class RedirectController extends BaseController
{
    public function home()
    {
        return redirect()->route('filament.admin.auth.login');
    }
} 