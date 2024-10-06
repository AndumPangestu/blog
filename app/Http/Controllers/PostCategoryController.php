<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\PostCategoryResource;
use App\Http\Resources\PostCategoryCollection;
use App\Http\Requests\PostCategoryCreateRequest;
use App\Http\Requests\PostCategoryUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class PostCategoryController extends Controller
{
    // Menampilkan semua kategori
    public function getAll(): PostCategoryCollection
    {
        $categories = PostCategory::all();
        if (!$categories) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "category not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new PostCategoryCollection($categories);
    }

    // Membuat kategori baru
    public function create(PostCategoryCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $categories = new PostCategory($data);
        $categories->save();


        return (new PostCategoryResource($categories))->response()->setStatusCode(201);
    }

    // Menampilkan kategori berdasarkan ID
    public function get($id): PostCategoryResource
    {
        $cacheKey = "post_category_{$id}"; // Buat kunci cache berdasarkan ID kategori

        // Mengambil kategori dari cache jika ada, jika tidak, ambil dari database dan cache hasilnya
        $category = Cache::remember($cacheKey, 60 * 60, function () use ($id) {
            return PostCategory::where('id', $id)->first();
        });

        if (!$category) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "category not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new PostCategoryResource($category);
    }


    // Memperbarui kategori
    public function update(PostCategoryUpdateRequest $request, $id): PostCategoryResource
    {
        $category = PostCategory::where('id', $id)->first();
        if (!$category) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "category not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $category->fill($data);
        $category->save();

        return new PostCategoryResource($category);
    }

    // Menghapus kategori
    public function delete($id): JsonResponse
    {
        $category = PostCategory::where('id', $id)->first();
        if (!$category) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "category not found"
                    ]
                ]
            ])->setStatusCode(404));
        }


        $category->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
