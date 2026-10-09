<?php
namespace App\Controllers;
use App\Models\UserModel;
final class AuthController extends BaseController
{
    private const MAX_ATTEMPTS = 5;
    private const WINDOW_SECONDS = 300;
    public function login()
    {
        if (session()->get('auth_user_id')) { return redirect()->to('/customers'); }
        return view('auth/login', ['title' => 'Staff sign in']);
    }
    public function attempt()
    {
        $username = trim((string) $this->request->getPost('username'));
        $attempts = array_values(array_filter(session()->get('login_attempts') ?? [], static fn (int $time): bool => $time > time() - self::WINDOW_SECONDS));
        if (count($attempts) >= self::MAX_ATTEMPTS) {
            session()->set('login_attempts', $attempts);
            return redirect()->back()->withInput()->with('error', 'Too many attempts. Try again in a few minutes.');
        }
        if (! $this->validate(['username' => 'required|max_length[50]', 'password' => 'required'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $model = new UserModel();
        $user = $model->where('LOWER(username)', mb_strtolower($username))->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            $attempts[] = time(); session()->set('login_attempts', $attempts);
            return redirect()->back()->withInput()->with('error', 'The username or password is incorrect.');
        }
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $model->update($user['id'], ['password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT)]);
        }
        session()->regenerate(true);
        session()->set(['auth_user_id' => (int) $user['id'], 'auth_user_name' => $user['full_name'], 'auth_user_role' => $user['role']]);
        session()->remove('login_attempts');
        $destination = (string) (session()->get('intended_url') ?? '/customers'); session()->remove('intended_url');
        if (! preg_match('#^/(customers|users)(?:/[0-9]+/edit|/new)?$#', $destination)) { $destination = '/customers'; }
        return redirect()->to($destination)->with('success', 'Signed in successfully.');
    }
    public function logout()
    {
        session()->remove(['auth_user_id', 'auth_user_name', 'auth_user_role', 'intended_url', 'login_attempts']);
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been signed out.');
    }
}
