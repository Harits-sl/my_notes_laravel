<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'url'           => 'sometimes|url|max:255',
      'title'         => 'sometimes|string|max:255',
      'desc'          => 'nullable|string',
      'image'         => 'nullable|string|max:255',
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
