<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);
$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('about', 'Home::about', ['as' => 'about']);
$routes->get('health', 'Home::health', ['as' => 'health']);
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attempt', ['as' => 'login.attempt']);
$routes->post('logout', 'AuthController::logout', ['as' => 'logout']);

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('customers', 'CustomersController::index');
    $routes->get('customers/new', 'CustomersController::new');
    $routes->post('customers', 'CustomersController::create');
    $routes->get('customers/(:num)/edit', 'CustomersController::edit/$1');
    $routes->post('customers/(:num)', 'CustomersController::update/$1');
    $routes->get('users', 'UsersController::index');
    $routes->get('users/new', 'UsersController::new');
    $routes->post('users', 'UsersController::create');
    $routes->get('users/(:num)/edit', 'UsersController::edit/$1');
    $routes->post('users/(:num)', 'UsersController::update/$1');
});
