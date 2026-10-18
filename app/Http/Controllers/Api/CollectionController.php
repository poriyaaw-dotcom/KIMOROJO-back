<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;

class CollectionController extends Controller
{
    public function index(): JsonResponse
    {
        $collections = Collection::all();
        
        return response()->json([
            'success' => true,
            'data' => $collections,
        ]);
    }
}
