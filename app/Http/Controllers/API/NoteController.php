<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        $data = Note::get();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $url = $request->input('url');

        if (!$url) {
            return response()->json(['error' => 'URL tidak ditemukan'], 400);
        }

        $data = $this->embedService->getMetaData($url);
        // jika data dari instagram
        if ($data['provider_name'] == 'Instagram') {
            $parsedUrl = parse_url($url);
            $segments = explode('/', trim($parsedUrl['path'], '/')); // hasil: ['reel atau p', 'DO1KzZsEZEw']
            $type = $segments[0] ?? null; // 'reel' atau 'p' atau 's'

            // jika tipe instagram p redirect ke /media?size=m untuk mengambil full image
            // jika tidak langsung save image dari metadata
            if ($type == 'p') {
                // ubah data image ke filename
                $data['image'] = $this->saveImage($this->getFullImageInstagram($parsedUrl));
            } else {
                $data['image'] = $this->saveImage($data['image']);
            }
        } else {
            // ubah data image ke filename
            $data['image'] = $this->saveImage($data['image']);
        }

        Note::create($data);
        return response()->json($data);
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
    public function destroy(Note $note)
    {
        //
    }

    private function saveImage($url)
    {
        $response = Http::get($url);
        // Tentukan nama file unik
        $filename = hash('md5', Str::random(20)) . '.jpg';
        // Simpan ke folder public/storage/images
        Storage::disk('public')->put('images/' . $filename, $response->body());

        return $filename;
    }

    private function getFullImageInstagram($parsedUrl)
    {
        // Ambil bagian path-nya (misal: "/p/DQ4ROThCdu1/")
        $base = rtrim($parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'], '/');
        $response = Http::withOptions(['allow_redirects' => true, 'on_stats' => function ($stats) use (&$finalUrl) {
            // Ambil final URI setelah request selesai
            $finalUrl = (string) $stats->getEffectiveUri();
        },])
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ])
            ->get($base . '/media?size=m');

        return $finalUrl;
    }
}
