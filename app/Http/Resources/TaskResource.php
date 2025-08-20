<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn($img) => [
                    'id'            => $img->id,
                    'original_name' => $img->original_name,
                    'url'           => $img->url,
                ]);
            }),
        ];
    }
}
