<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'nullable|string',
            'page' => 'nullable|numeric|min:1',
            'per_page' => 'nullable|numeric|in:10,25,50,100'
        ]);

        $category = Category::when($request->search, function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%');
        })
        ->paginate($request->per_page ?? 10);

        return $this->success(
            200,
            'Berhasil menampilkan data kategori',
            $category
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:3|max:50|unique:categories',
        ]);

        $category = Category::create([
            'name' => ucwords($request->name),
        ]);

        return $this->success(
            200,
            'Berhasil menambah kategori',
            new CategoryResource($category)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        return $this->success(
            200,
            'Berhasil menampilkan detail kategori',
            new CategoryResource($category)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|min:3|max:50'
        ]);

        $category->name = ucwords($request->name);

        $category->update();

        return $this->success(
            200,
            'Berhasil mengubah kategori',
            new CategoryResource($category)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return $this->success(
            200,
            'Berhasil menghapus kategori'
        );
    }
}
