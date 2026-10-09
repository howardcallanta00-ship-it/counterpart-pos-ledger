<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
final class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session()->get('auth_user_id')) { return null; }
        $destination = '/' . trim($request->getUri()->getPath(), '/');
        session()->set('intended_url', preg_match('#^/[a-z0-9/_-]*$#i', $destination) ? $destination : '/customers');
        return redirect()->to('/login')->with('info', 'Sign in to continue.');
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void {}
}
