<?php

namespace Modules\Bookmark\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookmarkRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'url'           => 'required|url|max:255',
      'title'         => 'required|string|max:255',
      'description'   => 'nullable|string',
      'image_url'     => 'nullable|string|max:255',
      'provider_name' => 'nullable|string|max:100',

      'categories'        => 'nullable|array',
      'categories.*.id'   => 'nullable|integer|exists:categories,id',
      'categories.*.name' => 'nullable|string|max:50',
    ];
  }

  public function withValidator($validator): void
  {
    $validator->after(function ($validator) {
      foreach ($this->input('categories', []) as $index => $category) {
        if (
          is_null($category['id'] ?? null) &&
          empty($category['name'] ?? null)
        ) {
          $validator->errors()->add(
            "categories.$index",
            'Category must have id or name.'
          );
        }
      }
    });
  }
}
