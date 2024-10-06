<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\BookmarkRequest;
use App\Http\Resources\BookmarkResource;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookmarkController extends Controller
{
    // Membuat bookmark baru
    public function create(BookmarkRequest $request): JsonResponse
    {
        $data = $request->validated();

        $bookmark = new Bookmark($data);
        $bookmark->user_id = Auth::id();
        $bookmark->save();

        return (new BookmarkResource($bookmark))->response()->setStatusCode(201);
    }

    public function get(): BookmarkResource
    {

        $userId = Auth::id();
        $cacheKey = "user_bookmark_{$userId}";


        $bookmark = Cache::remember($cacheKey, 60 * 60, function () use ($userId) {
            return Bookmark::where('user_id', $userId)->first();
        });

        if (!$bookmark) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "bookmark not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new BookmarkResource($bookmark);
    }

    public function delete($id): JsonResponse
    {

        $bookmark = Bookmark::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$bookmark) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "bookmark not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $bookmark->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
