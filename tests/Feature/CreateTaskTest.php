<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_task_with_valid_data_and_images(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'title'       => 'Task title',
            'description' => 'Task description text',
            'status'      => TaskStatus::Pending->value,
            'images'      => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.png'),
            ],
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'Task title']);

        $this->assertDatabaseHas('tasks', ['title' => 'Task title']);
        $this->assertDatabaseCount('task_images', 2);

        $paths = collect($response->json('data.images'))->pluck('path');
        foreach ($paths as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    #[Test]
    public function it_rejects_invalid_data_and_invalid_images(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'title'       => '',
            'description' => '',
            'images'      => [
                UploadedFile::fake()->create('not-an-image.pdf', 10, 'application/pdf'),
            ],
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description', 'images.0']);
    }
}
