<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_posts'      => Post::count(),
            'published_posts'  => Post::published()->count(),
            'draft_posts'      => Post::where('status', 'draft')->count(),
            'total_comments'   => Comment::count(),
            'pending_comments' => Comment::pending()->count(),
            'total_categories' => Category::count(),
            'total_views'      => Post::sum('views'),
        ];

        $topPosts = Post::published()
            ->orderByDesc('views')
            ->take(5)
            ->get();

        $recentComments = Comment::with('post')
            ->latest()
            ->take(6)
            ->get();

        $recentPosts = Post::with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'topPosts', 'recentComments', 'recentPosts'));
    }
}
