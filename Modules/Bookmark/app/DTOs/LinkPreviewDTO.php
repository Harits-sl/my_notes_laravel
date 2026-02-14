<?php

namespace Modules\Bookmark\DTOs;

class LinkPreviewDTO
{
  public function __construct(
    public string $url,
    public ?string $title = null,
    public ?string $description = null,
    public ?string $image_url = null,
    public ?string $provider_name = null,
  ) {}
}
