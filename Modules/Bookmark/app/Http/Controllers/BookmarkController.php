<?php

namespace Modules\Bookmark\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Modules\Bookmark\Http\Requests\StoreBookmarkRequest;
use Modules\Bookmark\Http\Resources\BookmarkResource;
use Modules\Bookmark\Models\Bookmark;
use Modules\Bookmark\Models\Category;
use Modules\Bookmark\Services\EmbedService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class BookmarkController extends Controller
{
    protected $embedService;

    public function __construct(EmbedService $embedService)
    {
        $this->embedService = $embedService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookmarks = Bookmark::with('categories')->latest()->get();

        return BookmarkResource::collection($bookmarks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookmarkRequest $request)
    {
        if ($request->image_url !== null) {
            $this->saveImage($request->image_url);
        }

        $bookmark = Bookmark::create([
            'url'         => $request->url,
            'title'       => $request->title,
            'description' => $request->description,
            'image_url'   => $request->image_url,
        ]);

        // sync categories
        $categoryIds = $this->resolveCategories(
            $request->input('categories', [])
        );

        $bookmark->categories()->sync($categoryIds);

        return ApiResponse::success(
            new BookmarkResource($bookmark->load('categories')),
            'Bookmark berhasil disimpan',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Bookmark $bookmark)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        Bookmark::findOrFail($id)->delete();

        return response()->noContent();
    }

    private function saveImage($url)
    {
        $response = Http::get($url);
        $filename = hash('md5', Str::random(20)) . '.jpg';
        Storage::disk('public')->put('images/' . $filename, $response->body());

        return $filename;
    }

    protected function resolveCategories(array $categories): array
    {
        return collect($categories)
            ->filter(fn($category) => is_array($category))
            ->map(function ($category) {

                if (!empty($category['id'])) {
                    return (int) $category['id'];
                }

                if (!empty($category['name'])) {
                    $name = ucfirst(strtolower(trim($category['name'])));

                    return Category::firstOrCreate([
                        'name' => $name,
                    ])->id;
                }

                return null;
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
