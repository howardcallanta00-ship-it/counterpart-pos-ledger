<?php
namespace App\Services\Avatar;
use RuntimeException;
final class LocalAvatarStorage implements AvatarStorage
{
    private string $directory;
    public function __construct() { $this->directory = FCPATH . 'uploads/avatars'; }
    public function available(): bool { return is_dir($this->directory) ? is_writable($this->directory) : @mkdir($this->directory, 0755, true); }
    public function store(string $filename, string $contents): void
    {
        if (! $this->available() || file_put_contents($this->directory . DIRECTORY_SEPARATOR . basename($filename), $contents, LOCK_EX) === false) { throw new RuntimeException('Avatar storage is not writable.'); }
    }
    public function delete(string $filename): void { $path = $this->directory . DIRECTORY_SEPARATOR . basename($filename); if (is_file($path)) { @unlink($path); } }
    public function url(?string $filename): string { return $filename ? base_url('uploads/avatars/' . rawurlencode($filename)) : base_url('assets/avatar-placeholder.svg'); }
    public function notice(): ?string { return $this->available() ? null : 'Avatar uploads are unavailable because local storage is not writable.'; }
}
