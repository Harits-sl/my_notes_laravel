<?php

namespace App\Http\Controllers;

use App\Http\Resources\LinkPreviewResource;
use App\Http\Responses\ApiResponse;
use App\Services\EmbedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LinkPreviewController extends Controller
{
  protected $embedService;

  public function __construct(EmbedService $embedService)
  {
    $this->embedService = $embedService;
  }

  public function preview(Request $request)
  {
    $url = $request->input('url');
    if (!$url) {
      return response()->json(['error' => 'URL tidak ditemukan'], 400);
    }

    $data = $this->embedService->getMetaData($url);
    // jika data dari instagram
    if ($data->provider_name == 'Instagram') {
      $parsedUrl = parse_url($url);
      $segments = explode('/', trim($parsedUrl['path'], '/')); // hasil: ['reel atau p', 'DO1KzZsEZEw']
      $type = $segments[0] ?? null; // 'reel' atau 'p' atau 's'

      // jika tipe instagram p redirect ke /media?size=m untuk mengambil full image
      if ($type == 'p') {
        // ubah data image ke filename
        $data->image_url = $this->getFullImageInstagram($parsedUrl);
      }
    }

    return ApiResponse::success(
      new LinkPreviewResource($data),
      'Preview berhasil didapat',
      200
    );
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
