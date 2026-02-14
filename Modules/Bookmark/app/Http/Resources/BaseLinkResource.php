<?php

namespace Modules\Bookmark\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseLinkResource extends JsonResource
{
  protected function base(): array
  {
    return [
      'url'           => $this->url ?? null,
      'title'         => $this->title ?? null,
      'description'   => $this->description ?? null,
      'provider_name' => $this->provider_name ?? null,
      'image_url'     => $this->image_url ?? null,
    ];
  }
}
