<?php

namespace App\Http\Resources;


class LinkPreviewResource extends BaseLinkResource
{
  public function toArray($request): array
  {
    return $this->base();
  }
}
