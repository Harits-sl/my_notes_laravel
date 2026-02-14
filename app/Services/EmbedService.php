<?php

namespace App\Services;

use App\DTOs\LinkPreviewDTO;
use Embed\Embed;

class EmbedService
{
  public function getMetaData(string $url): LinkPreviewDTO
  {
    try {
      $embed = new Embed();
      $info = $embed->get($url);

      return new LinkPreviewDTO(
        url: $url,
        title: $info->title ?? null,
        description: $info->description ?? null,
        image_url: $info->image ?? null,
        provider_name: $info->providerName ?? null,
      );
    } catch (\Throwable $e) {
      throw new \RuntimeException(
        'Gagal mengambil metadata',
        previous: $e
      );
    }
  }
}
