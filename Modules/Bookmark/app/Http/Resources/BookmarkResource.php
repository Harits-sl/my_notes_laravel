<?php

namespace Modules\Bookmark\Http\Resources;

class BookmarkResource extends BaseLinkResource
{
  public function toArray($request)
  {
    return array_merge(
      $this->base(),
      [
        'id' => $this->id,
        'categories' => CategoryResource::collection(
          $this->whenLoaded('categories')
        ),
      ]
    );
  }
}
