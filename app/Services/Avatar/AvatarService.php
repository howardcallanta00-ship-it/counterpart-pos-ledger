<?php
namespace App\Services\Avatar;
use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;
final class AvatarService
{
    public function __construct(private readonly AvatarStorage $storage) {}
    public static function make(): self
    {
        return new self(env('avatar.driver', 'local') === 'object' ? new ObjectAvatarStorage() : new LocalAvatarStorage());
    }
    public function storage(): AvatarStorage { return $this->storage; }
    public function process(?UploadedFile $file): ?string
    {
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) { return null; }
        if (! $this->storage->available()) { throw new RuntimeException((string) $this->storage->notice()); }
        if ($file->getSize() > 2 * 1024 * 1024) { throw new RuntimeException('Avatar files must be 2 MB or smaller.'); }
        if (! $file->isValid()) { throw new RuntimeException('The avatar upload did not complete.'); }
        $info = @getimagesize($file->getTempName());
        if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png'], true)) { throw new RuntimeException('Choose a valid JPEG or PNG image.'); }
        $source = $info['mime'] === 'image/jpeg' ? @imagecreatefromjpeg($file->getTempName()) : @imagecreatefrompng($file->getTempName());
        if (! $source) { throw new RuntimeException('The image could not be decoded.'); }
        if ($info['mime'] === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($file->getTempName())['Orientation'] ?? 1;
            if ($orientation === 3) { $source = imagerotate($source, 180, 0); }
            if ($orientation === 6) { $source = imagerotate($source, -90, 0); }
            if ($orientation === 8) { $source = imagerotate($source, 90, 0); }
        }
        $size = min(imagesx($source), imagesy($source)); $x = (int) ((imagesx($source) - $size) / 2); $y = (int) ((imagesy($source) - $size) / 2);
        $target = imagecreatetruecolor(256, 256); imagealphablending($target, false); imagesavealpha($target, true);
        imagecopyresampled($target, $source, 0, 0, $x, $y, 256, 256, $size, $size);
        ob_start(); imagepng($target, null, 8); $contents = (string) ob_get_clean(); imagedestroy($source); imagedestroy($target);
        $filename = bin2hex(random_bytes(16)) . '.png'; $this->storage->store($filename, $contents); return $filename;
    }
}
