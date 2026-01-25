<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\NavigationService;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    public function __construct(
        private NavigationService $navigationService
    ) {}

    public function index(Request $request)
    {
        /* $rolId = $request->user()->rol_id; */
        $rolId = $request->query('rol_id');

        return response()->json(
            $this->navigationService->getNavigationByRol($rolId)
        );
    }
}
