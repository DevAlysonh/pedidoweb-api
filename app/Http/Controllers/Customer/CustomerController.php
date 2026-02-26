<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ], 200);
    }
}
