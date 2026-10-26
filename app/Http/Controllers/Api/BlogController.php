<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    public function index(): JsonResponse
    {
        // Get all blogs, ordered by newest first
        $blogs = Blog::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $blogs
        ]);
    }

    public function show($slug): JsonResponse
    {
        // Find by slug, no published check
        $blog = Blog::where('slug', $slug)->first();
        
        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $blog
        ]);
    }
}