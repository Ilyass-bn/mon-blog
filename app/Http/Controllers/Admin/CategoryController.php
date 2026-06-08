<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /* ── Index ── */

    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /* ── Create ── */

    public function create()
    {
        return view('admin.categories.create');
    }

    /* ── Store ── */

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie créée.');
    }

    /* ── Edit ── */

    public function edit(Category $category)
    {
        return view('admin.categories.create', compact('category'));
    }

    /* ── Update ── */

    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request);
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour.');
    }

    /* ── Destroy ── */

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée.');
    }

    /* ── Private helper ── */

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
    }
}
