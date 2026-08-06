<?php

namespace Tests\Feature;

use App\Services\AvatarUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarUploadServiceTest extends TestCase
{
    public function test_store_writes_a_resized_jpeg_via_the_storage_facade(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.png', 800, 800);

        $path = (new AvatarUploadService)->store($file);

        $this->assertStringStartsWith('avatars/', $path);
        $this->assertStringEndsWith('.jpg', $path);
        Storage::disk('public')->assertExists($path);

        $image = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(400, $image[0]);
        $this->assertSame(400, $image[1]);
    }
}
