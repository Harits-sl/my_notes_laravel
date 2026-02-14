<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Resources\NoteResource;
use App\Http\Responses\ApiResponse;
use App\Models\Note;
use App\Services\EmbedService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
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
        $notes = Note::with('categories')->latest()->get();

        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request)
    {
        if ($request->image_url !== null) {
            $this->saveImage($request->image_url);
        }

        $note = Note::create([
            'url'         => $request->url,
            'title'       => $request->title,
            'description' => $request->description,
            'image_url'   => $request->image_url,
        ]);

        // sync categories
        $categoryIds = $this->resolveCategories(
            $request->input('categories', [])
        );

        $note->categories()->sync($categoryIds);

        return ApiResponse::success(
            new NoteResource($note->load('categories')),
            'Note berhasil disimpan',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        Note::findOrFail($id)->delete();

        return response()->noContent();
    }

    /**
     * Download dan simpan gambar dari URL ke storage publik.
     *
     * Function ini akan:
     * - Mengambil file gambar dari URL menggunakan HTTP client Laravel
     * - Membuat nama file unik menggunakan hash MD5
     * - Menyimpan gambar ke folder `storage/app/public/images`
     *
     * Pastikan:
     * - URL yang diberikan valid dan dapat diakses publik
     *
     * @param  string  $url
     *         URL gambar yang akan diunduh dan disimpan
     *
     * @return string
     *         Nama file gambar yang berhasil disimpan
     *
     * @throws \Illuminate\Http\Client\RequestException
     *         Jika request HTTP ke URL gagal
     */
    private function saveImage($url)
    {
        $response = Http::get($url);
        // Tentukan nama file unik
        $filename = hash('md5', Str::random(20)) . '.jpg';
        // Simpan ke folder public/storage/images
        Storage::disk('public')->put('images/' . $filename, $response->body());

        return $filename;
    }

    /**
     * Resolve dan normalisasi daftar kategori (existing & new).
     *
     * Method ini menerima payload kategori dari request, lalu:
     * - Menggunakan ID jika kategori sudah ada
     * - Membuat kategori baru jika ID kosong tapi memiliki nama
     * - Mengabaikan data yang tidak valid
     * - Menghapus duplikasi ID kategori
     *
     * Contoh payload:
     * [
     *   ['id' => 1, 'name' => 'Backend'],
     *   ['id' => null, 'name' => 'Laravel'],
     *   ['id' => 2],
     * ]
     *
     * Hasil:
     * [
     *   1,
     *   3, // ID kategori "Laravel" yang baru dibuat
     *   2
     * ]
     *
     * @param  array<int, array{id?: int|null, name?: string|null}>  $categories
     *         Daftar kategori dari request
     *
     * @return array<int, int>
     *         Array ID kategori yang valid dan unik
     */
    protected function resolveCategories(array $categories): array
    {
        return collect($categories)
            ->filter(fn($category) => is_array($category))
            ->map(function ($category) {

                // 1️⃣ Existing category (ID takes precedence)
                if (!empty($category['id'])) {
                    return (int) $category['id'];
                }

                // 2️⃣ New category (create if name exists)
                if (!empty($category['name'])) {
                    $name = ucfirst(strtolower(trim($category['name'])));

                    return Category::firstOrCreate([
                        'name' => $name,
                    ])->id;
                }

                // 3️⃣ Invalid category payload
                return null;
            })
            ->filter()   // Remove null values
            ->unique()   // Remove duplicate IDs
            ->values()   // Reindex array
            ->toArray();
    }
}
