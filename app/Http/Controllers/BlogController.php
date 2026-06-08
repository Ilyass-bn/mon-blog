<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Shared sidebar data injected into every view.
     */
    private function sidebarData(): array
    {
        return [
            'categories' => Category::withCount(['posts' => fn ($q) => $q->published()])
                ->orderBy('name')
                ->get(),
        ];
    }

    /* ── Home / Blog index ── */

    public function index(Request $request)
    {
        $featured = Post::published()
            ->featured()
            ->with(['author', 'category', 'approvedComments'])
            ->latest('published_at')
            ->take(4)
            ->get();

        $posts = Post::published()
            ->with(['author', 'category', 'approvedComments'])
            ->latest('published_at')
            ->paginate(9);

        $recentPosts = Post::published()
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('blog.index', array_merge($this->sidebarData(), compact('featured', 'posts', 'recentPosts')));
    }

    /* ── Single post ── */

    public function show(string $slug)
    {
        $post = Post::published()
            ->with(['author', 'category', 'approvedComments'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views (simple counter — no session dedup for brevity)
        $post->increment('views');

        // Related posts: same category, excluding current
        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('blog.show', array_merge($this->sidebarData(), compact('post', 'related')));
    }

    /* ── Category archive ── */

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['author', 'category', 'approvedComments'])
            ->latest('published_at')
            ->paginate(9);

        $recentPosts = Post::published()->latest('published_at')->take(5)->get();

        return view('blog.category', array_merge($this->sidebarData(), compact('category', 'posts', 'recentPosts')));
    }

    /* ── Search ── */

    public function search(Request $request)
    {
        $q = $request->input('q', '');

        $posts = Post::published()
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('excerpt', 'like', "%{$q}%")
                      ->orWhere('content', 'like', "%{$q}%");
            })
            ->with(['author', 'category', 'approvedComments'])
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $recentPosts = Post::published()->latest('published_at')->take(5)->get();

        return view('blog.search', array_merge($this->sidebarData(), compact('posts', 'q', 'recentPosts')));
    }

    /* ── Store comment ── */

    public function storeComment(Request $request, int $postId)
    {
        $post = Post::published()->findOrFail($postId);

        $validated = $request->validate([
            'author_name'  => ['required', 'string', 'max:100'],
            'author_email' => ['required', 'email', 'max:255'],
            'content'      => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $post->comments()->create($validated);

        return back()->with('success', 'Votre commentaire a été soumis et sera publié après modération. Merci !');
    }
}
