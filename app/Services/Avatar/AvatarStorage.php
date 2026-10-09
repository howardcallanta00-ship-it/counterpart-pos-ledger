<?php
namespace App\Services\Avatar;
interface AvatarStorage
{
    public function available(): bool;
    public function store(string $filename, string $contents): void;
    public function delete(string $filename): void;
    public function url(?string $filename): string;
    public function notice(): ?string;
}
