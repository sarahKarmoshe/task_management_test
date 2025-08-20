<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');
        return $this->user()->can('update', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:160'],
            'description' => ['sometimes', 'string'],
            'status' => ['sometimes', new Enum(TaskStatus::class)],
            'images' => ['sometimes', 'array', 'min:1'],          // new images to append
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'delete_image_ids' => ['sometimes', 'array'],
            'delete_image_ids.*' => ['integer', 'min:1'],
        ];
    }
}
