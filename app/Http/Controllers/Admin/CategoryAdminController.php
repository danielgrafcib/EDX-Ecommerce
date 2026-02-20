<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->with('parent')->orderBy('name')->paginate(20);
        $parents = Category::orderBy('name')->get();

        return view('admin.categories.index', compact('categories','parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Catégorie créée avec succès.');
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['required', 'string', 'unique:categories,slug,'.$category->id],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Catégorie mise à jour.');
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', 'Catégorie supprimée.');
    }
}






















