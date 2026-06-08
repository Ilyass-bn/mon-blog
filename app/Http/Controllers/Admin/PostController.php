<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /* ── Index ── */

    public function index(Request $request)
    {
        $query = Post::with(['author', 'category'])
            ->withCount('comments');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        $posts      = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /* ── Create ── */

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.create', compact('categories'));
    }

    /* ── Store ── */

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['slug']    = Str::slug($validated['title']);

        Post::create($validated);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article créé avec succès.');
    }

    /* ── Edit ── */

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.create', compact('post', 'categories'));
    }

    /* ── Update ── */

    public function update(Request $request, Post $post)
    {
        $validated = $this->validatePost($request);

        if ($request->hasFile('cover_image')) {
            // Delete old image
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $post->update($validated);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article mis à jour.');
    }

    /* ── Toggle status ── */

    public function toggle(Post $post)
    {
        $post->status = $post->status === 'published' ? 'draft' : 'published';
        $post->save();

        return back()->with('success', 'Statut de l\'article mis à jour.');
    }

    /* ── Destroy ── */

    public function destroy(Post $post)
    {
        if ($post->cover_image) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Article supprimé.');
    }

    /* ── Private helper ── */

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'content'     => ['required', 'string'],
            'status'      => ['required', 'in:draft,published'],
            'featured'    => ['nullable', 'boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
