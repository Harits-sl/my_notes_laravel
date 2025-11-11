<?php

namespace App\Http\Controllers;

use App\Services\EmbedService;
use Illuminate\Http\Request;

class LinkPreviewController extends Controller
{
  protected $embedService;

  public function __construct(EmbedService $embedService)
  {
    $this->embedService = $embedService;
  }

  public function preview(Request $request)
  {

    $url = $request->query('url');
    if (!$url) {
      return response()->json(['error' => 'URL tidak ditemukan'], 400);
    }

    $data = $this->embedService->getMetaData($url);
    return response()->json($data);
  }
}
