<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\PostCollection;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostController extends Controller
{
    // Menampilkan semua posts
    public function getAll(): PostCollection
    {
        $posts = Post::with('categories')->get();

        if (!$posts) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new PostCollection($posts);
    }

    // Menampilkan post berdasarkan ID
    public function get($id): PostResource
    {
        $post = Post::where('id', $id)->first();
        if (!$post) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "category not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        return new PostResource($post);
    }

    // Membuat post baru
    public function create(PostCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = Auth::user();

        $post = new Post($data);
        $post->user_id = $user->id;
        $post->slug = Str::slug($post->title);
        $post->save();

        if (!empty($data['categories'])) {
            $post->categories()->attach($data['categories']);
        }

        // Menyimpan gambar jika ada
        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('images', 'public');
        }


        return (new PostResource($post))->response()->setStatusCode(201);
    }

    // Memperbarui post
    public function update(PostUpdateRequest $request, $id): PostResource
    {
        $post = Post::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$post) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ])->setStatusCode(404));
        }


        $data = $request->validated();
        $post->fill($data);
        $post->save();


        if (!empty($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }

        if ($request->hasFile('image')) {
            // Hapus gambar lama (opsional, jika tidak ingin menyimpan lebih dari satu)
            $post->clearMediaCollection('images');

            // Tambahkan gambar baru
            $post->addMediaFromRequest('image')->toMediaCollection('images', 'public');
        }

        return new PostResource($post);
    }

    // Menghapus post
    public function delete($id)
    {
        $post = Post::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$post) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $post->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function getUserPosts($user_id): PostResource
    {

        if ($user_id != Auth::id()) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Unauthorized action."
                    ]
                ]
            ])->setStatusCode(403));
        }

        $cacheKey = 'user_posts_' . $user_id;


        $post = Cache::remember($cacheKey, 60 * 60, function () use ($user_id) {

            return Post::where('user_id', $user_id)->first();
        });


        if (!$post) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Post not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        // Kembalikan post sebagai PostResource
        return new PostResource($post);
    }
}
