<?php

namespace App\Http\Resources;

class NoteResource extends BaseLinkResource
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
