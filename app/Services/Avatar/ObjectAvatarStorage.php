<?php
namespace App\Services\Avatar;
use RuntimeException;
final class ObjectAvatarStorage implements AvatarStorage
{
    private string $putTemplate;
    private string $deleteTemplate;
    private string $publicBase;
    private string $token;
    public function __construct()
    {
        $this->putTemplate = (string) env('avatar.object.putUrlTemplate', '');
        $this->deleteTemplate = (string) env('avatar.object.deleteUrlTemplate', '');
        $this->publicBase = rtrim((string) env('avatar.object.publicBaseUrl', ''), '/');
        $this->token = (string) env('avatar.object.bearerToken', '');
    }
    public function available(): bool { return $this->putTemplate !== '' && $this->publicBase !== ''; }
    public function store(string $filename, string $contents): void { $this->request('PUT', str_replace('{key}', rawurlencode($filename), $this->putTemplate), $contents); }
    public function delete(string $filename): void { if ($this->deleteTemplate !== '') { $this->request('DELETE', str_replace('{key}', rawurlencode($filename), $this->deleteTemplate), ''); } }
    public function url(?string $filename): string { return $filename ? $this->publicBase . '/' . rawurlencode($filename) : base_url('assets/avatar-placeholder.svg'); }
    public function notice(): ?string { return $this->available() ? null : 'Durable avatar storage is not configured. Accounts remain available, but avatar changes are disabled.'; }
    private function request(string $method, string $url, string $body): void
    {
        if (! $this->available()) { throw new RuntimeException((string) $this->notice()); }
        $headers = ['Content-Type: image/png']; if ($this->token !== '') { $headers[] = 'Authorization: Bearer ' . $this->token; }
        $context = stream_context_create(['http' => ['method' => $method, 'header' => implode("\r\n", $headers), 'content' => $body, 'timeout' => 15, 'ignore_errors' => true]]);
        $result = @file_get_contents($url, false, $context); $status = $http_response_header[0] ?? '';
        if ($result === false || ! preg_match('/\s2\d\d\s/', $status)) { throw new RuntimeException('Durable avatar storage rejected the upload.'); }
    }
}
