<?php

namespace Modules\Bookmark\Http\Resources;

class LinkPreviewResource extends BaseLinkResource
{
  public function toArray($request): array
  {
    return $this->base();
  }
}
