<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create',Task::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'status'      => ['nullable', new Enum(TaskStatus::class)],
            'images'      => ['sometimes','array','min:1'],
            'images.*'    => ['file','image','mimes:jpg,jpeg,png,webp','max:5120'],
        ];
    }
}
