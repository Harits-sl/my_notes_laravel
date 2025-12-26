<?php

namespace App\Services;

use Embed\Embed;

class EmbedService
{
  public function getMetaData(string $url): array
  {
    try {
      $embed = new Embed();
      $info = $embed->get($url);

      return [
        'url' => $url,
        'title' => $info->title ?? null,
        'desc' => $info->description ?? null,
        'image_url' => $info->image ?? null,
        'provider_name' => $info->providerName ?? null,
      ];
    } catch (\Throwable $e) {
      return [
        'error' => 'Gagal mengambil metadata',
        'detail' => $e->getMessage(),
      ];
    }
  }
}
