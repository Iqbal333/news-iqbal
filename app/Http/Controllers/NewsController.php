<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\NewsResource;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'nullable|string',
            'page' => 'nullable|numeric|min:1',
            'per_page' => 'nullable|numeric|in:10,25,50,100',
        ]);

        $news = News::when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })
        ->paginate($request->per_page ?? 10);

        return $this->success(
            200,
            'Berhasil menampilkan data berita',
            $news
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|numeric',
            'title'       => 'required|string|max:50|unique:news,title',
            'content'     => 'required|string',
            'is_draft'    => 'required|in:0,1'
        ]);

        $news = News::create([
            'category_id' => $request->category_id,
            'title'       => ucwords($request->title),
            'content'     => ucwords($request->content),
            'is_draft'    => $request->is_draft
        ]);

        return $this->success(
            200,
            'Berhasil menambah berita',
            new NewsResource($news)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = News::with(['category'])->findOrFail($id);

        return $this->success(
            200,
            'Berhasil menampilkan detail berita',
            new NewsResource($news)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $news = News::findOrFail($id);

        $this->validate($request, [
            'category_id' => 'required|numeric',
            'title'       => 'required|string|max:50|',
                            Rule::unique('news', 'title')->ignore($news->id),
            'content'     => 'required|string',
            'is_draft'    => 'required|in:0,1'
        ]);

        $news->category_id = $request->category_id;
        $news->title       = ucwords($request->title);
        $news->content     = ucwords($request->content);
        $news->is_draft    = $request->is_draft;

        $news->update();

        return $this->success(
            200,
            'Berhasil mengubah berita',
            new NewsResource($news)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::findOrFail($id);

        $news->delete();

        return $this->success(
            200,
            'Berhasil menghapus berita'
        );
    }
}
