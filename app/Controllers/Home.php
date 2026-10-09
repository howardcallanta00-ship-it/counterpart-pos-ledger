<?php

namespace App\Controllers;

final class Home extends BaseController
{
    public function index(): string { return view('public/home', ['title' => 'Counterpart POS']); }
    public function about(): string { return view('public/about', ['title' => 'About']); }
    public function health() { return $this->response->setJSON(['status' => 'ok', 'service' => 'counterpart-pos']); }
}
