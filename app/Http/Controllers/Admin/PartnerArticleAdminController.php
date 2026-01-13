<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageOptimizer;

class PartnerArticleAdminController extends Controller
{
    public function index(int $partnerId)
    {
        $partner = Partner::findOrFail($partnerId);
        $q = request('q');
        $articles = PartnerArticle::where('partner_id',$partner->id)
            ->when($q, fn($qr) => $qr->where('title','like','%'.$q.'%'))
            ->latest()->paginate(20);
        return view('admin.partners.articles.index', compact('partner','articles'));
    }

    public function create(int $partnerId)
    {
        $partner = Partner::findOrFail($partnerId);
        return view('admin.partners.articles.create', compact('partner'));
    }

    public function store(Request $request, int $partnerId)
    {
        $partner = Partner::findOrFail($partnerId);
        $validated = $request->validate([
            'title' => ['required','string'],
            'content' => ['nullable','string'],
            'published_at' => ['nullable','date'],
            'cover' => ['nullable','image','max:5120'],
            'categories' => ['nullable','string'],
            'tags' => ['nullable','string'],
        ]);
        $article = PartnerArticle::create(array_merge(collect($validated)->except(['cover','categories','tags'])->all(), ['partner_id'=>$partner->id]));
        if ($request->hasFile('cover')) {
            $optimized = ImageOptimizer::process($request->file('cover'), 'partners/articles');
            $article->cover_path = $optimized['large_jpg'];
            $article->save();
        }
        $this->syncTaxonomy($article, $validated['categories'] ?? '', $validated['tags'] ?? '');
        return redirect()->route('admin.partners.articles.index',$partner->id)->with('status','Article créé.');
    }

    public function edit(int $partnerId, int $id)
    {
        $partner = Partner::findOrFail($partnerId);
        $article = PartnerArticle::where('partner_id',$partner->id)->findOrFail($id);
        return view('admin.partners.articles.edit', compact('partner','article'));
    }

    public function update(Request $request, int $partnerId, int $id)
    {
        $partner = Partner::findOrFail($partnerId);
        $article = PartnerArticle::where('partner_id',$partner->id)->findOrFail($id);
        $validated = $request->validate([
            'title' => ['required','string'],
            'content' => ['nullable','string'],
            'published_at' => ['nullable','date'],
            'cover' => ['nullable','image','max:5120'],
            'categories' => ['nullable','string'],
            'tags' => ['nullable','string'],
        ]);
        $article->update(collect($validated)->except(['cover','categories','tags'])->all());
        if ($request->hasFile('cover')) {
            if ($article->cover_path && str_starts_with($article->cover_path, '/storage/')) {
                $relative = substr($article->cover_path, strlen('/storage/'));
                Storage::disk('public')->delete($relative);
            }
            $optimized = ImageOptimizer::process($request->file('cover'), 'partners/articles');
            $article->cover_path = $optimized['large_jpg'];
            $article->save();
        }
        $this->syncTaxonomy($article, $validated['categories'] ?? '', $validated['tags'] ?? '');
        return redirect()->route('admin.partners.articles.edit',[$partner->id,$article->id])->with('status','Article mis à jour.');
    }

    public function destroy(int $partnerId, int $id)
    {
        $partner = Partner::findOrFail($partnerId);
        $article = PartnerArticle::where('partner_id',$partner->id)->findOrFail($id);
        if ($article->cover_path && str_starts_with($article->cover_path, '/storage/')) {
            $relative = substr($article->cover_path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $article->delete();
        return redirect()->route('admin.partners.articles.index',$partner->id)->with('status','Article supprimé.');
    }

    protected function syncTaxonomy(PartnerArticle $article, string $categories, string $tags): void
    {
        $catNames = collect(explode(',', $categories))->map(fn($n)=>trim($n))->filter()->unique();
        $tagNames = collect(explode(',', $tags))->map(fn($n)=>trim($n))->filter()->unique();

        $catIds = $catNames->map(function ($name) {
            $slug = str()->slug($name);
            return \App\Models\ArticleCategory::firstOrCreate(['slug'=>$slug], ['name'=>$name])->id;
        })->all();
        $tagIds = $tagNames->map(function ($name) {
            $slug = str()->slug($name);
            return \App\Models\ArticleTag::firstOrCreate(['slug'=>$slug], ['name'=>$name])->id;
        })->all();

        $article->categories()->sync($catIds);
        $article->tags()->sync($tagIds);
    }

    public function addImage(Request $request, int $partnerId, int $id)
    {
        $partner = Partner::findOrFail($partnerId);
        $article = PartnerArticle::where('partner_id',$partner->id)->findOrFail($id);
        $validated = $request->validate([
            'image' => ['required','image','max:5120'],
        ]);
        $optimized = ImageOptimizer::process($request->file('image'), 'partners/articles/gallery');
        \App\Models\PartnerArticleImage::create([
            'partner_article_id' => $article->id,
            'path' => $optimized['large_jpg'],
            'is_primary' => false,
        ]);
        return redirect()->route('admin.partners.articles.edit',[$partner->id,$article->id])->with('status','Image ajoutée.');
    }

    public function deleteImage(int $partnerId, int $id, int $imageId)
    {
        $partner = Partner::findOrFail($partnerId);
        $article = PartnerArticle::where('partner_id',$partner->id)->findOrFail($id);
        $image = \App\Models\PartnerArticleImage::where('partner_article_id',$article->id)->findOrFail($imageId);
        if ($image->path && str_starts_with($image->path, '/storage/')) {
            $relative = substr($image->path, strlen('/storage/'));
            Storage::disk('public')->delete($relative);
        }
        $image->delete();
        return redirect()->route('admin.partners.articles.edit',[$partner->id,$article->id])->with('status','Image supprimée.');
    }

    public function setPrimaryImage(int $partnerId, int $id, int $imageId)
    {
        $partner = Partner::findOrFail($partnerId);
        $article = PartnerArticle::where('partner_id',$partner->id)->findOrFail($id);
        $image = \App\Models\PartnerArticleImage::where('partner_article_id',$article->id)->findOrFail($imageId);
        \App\Models\PartnerArticleImage::where('partner_article_id',$article->id)->update(['is_primary'=>false]);
        $image->is_primary = true;
        $image->save();
        return redirect()->route('admin.partners.articles.edit',[$partner->id,$article->id])->with('status','Image principale définie.');
    }
}
