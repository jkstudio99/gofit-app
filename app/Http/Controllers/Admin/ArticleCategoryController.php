<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    /**
     * Display a listing of the article categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = ArticleCategory::withCount('articles')->get();
        return view('admin.article-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new article category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.article-categories.create');
    }

    /**
     * Store a newly created article category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:article_categories',
            'category_desc' => 'nullable|string|max:255',
        ]);

        ArticleCategory::create($request->all());

        return redirect()->route('admin.article-categories.index')
                         ->with('success', 'หมวดหมู่บทความถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Show the form for editing the specified article category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = ArticleCategory::findOrFail($id);
        return view('admin.article-categories.edit', compact('category'));
    }

    /**
     * Update the specified article category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:100|unique:article_categories,category_name,' . $id . ',category_id',
            'category_desc' => 'nullable|string|max:255',
        ]);

        $category = ArticleCategory::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('admin.article-categories.index')
                         ->with('success', 'หมวดหมู่บทความถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified article category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = ArticleCategory::findOrFail($id);

        // Check if the category has any articles
        if ($category->articles()->count() > 0) {
            return redirect()->route('admin.article-categories.index')
                           ->with('error', 'ไม่สามารถลบหมวดหมู่ได้เนื่องจากมีบทความที่ใช้หมวดหมู่นี้อยู่');
        }

        $category->delete();

        return redirect()->route('admin.article-categories.index')
                       ->with('success', 'หมวดหมู่บทความถูกลบเรียบร้อยแล้ว');
    }
}
