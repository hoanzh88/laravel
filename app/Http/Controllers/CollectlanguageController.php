<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CollectlanguageController extends Controller
{
    public function index(Request $request)
    {
        $DataAll = collect(__('messages.data'));
        dd($DataAll);
    }
}
