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

    /**
     * Regression test: Livewire's TemporaryUploadedFile resolves
     * getRealPath()/getPathname() against whatever disk the temp upload
     * lives on. When that disk is S3-backed (as it now is in production —
     * see config/livewire.php), those paths aren't real local files, so
     * handing the file object straight to Intervention Image used to throw
     * "The directory ... does not exist". This simulates that exact shape
     * (a file whose real path is bogus but that exposes ->get()) to make
     * sure AvatarUploadService reads bytes through ->get() instead.
     */
    public function test_store_works_when_the_uploaded_file_has_no_real_local_path(): void
    {
        Storage::fake('public');

        $gdImage = imagecreatetruecolor(800, 800);
        imagefill($gdImage, 0, 0, imagecolorallocate($gdImage, 100, 150, 200));
        ob_start();
        imagepng($gdImage);
        $bytes = ob_get_clean();
        imagedestroy($gdImage);

        $file = new class($bytes) extends UploadedFile
        {
            public function __construct(private string $bytes)
            {
                parent::__construct(tempnam(sys_get_temp_dir(), 'stub'), 'avatar.png', 'image/png', null, true);
            }

            public function getRealPath(): string
            {
                return '/nonexistent/s3-backed/path/avatar.png';
            }

            public function getPathname(): string
            {
                return $this->getRealPath();
            }

            public function get(): string
            {
                return $this->bytes;
            }
        };

        $path = (new AvatarUploadService)->store($file);

        Storage::disk('public')->assertExists($path);

        $image = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(400, $image[0]);
        $this->assertSame(400, $image[1]);
    }
}
