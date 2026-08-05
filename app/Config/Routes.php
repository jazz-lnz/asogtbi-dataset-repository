<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('about/platform', 'Home::aboutPlatform');
$routes->get('about/partners', 'Home::aboutPartners');

$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('auth/google', 'Auth::google');
$routes->get('auth/google/callback', 'Auth::googleCallback');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::sendResetLink');
$routes->get('reset-password', 'Auth::resetPassword');
$routes->post('reset-password', 'Auth::updatePassword');
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes): void {
    $routes->get('account/settings', 'Account::settings');
    $routes->post('account/settings', 'Account::updateSettings');
    $routes->post('notifications/read', 'Dashboard::readNotifications');
    $routes->get('notifications/poll', 'Dashboard::pollNotifications');
    $routes->get('dashboard', 'Dashboard::index');
    $routes->post('dashboard/notifications/read', 'Dashboard::readNotifications');
    $routes->get('portal/dashboard', 'Dashboard::portal');
    $routes->get('portal/datasets/(:num)', 'Dashboard::portalDataset/$1');
    $routes->post('portal/notifications/read', 'Dashboard::readPortalNotifications');
    $routes->get('portal/notifications/poll', 'Dashboard::pollNotifications');
    $routes->get('upload', 'DatasetUpload::create');
    $routes->post('upload', 'DatasetUpload::store');
    $routes->get('datasets/(:num)/edit', 'Datasets::edit/$1');
    $routes->post('datasets/(:num)/update', 'Datasets::update/$1');
    $routes->post('datasets/(:num)/archive', 'Datasets::archive/$1');
});

$routes->group('review/ethics', ['filter' => 'role:ethics_reviewer'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Reviews::index/ethics');
    $routes->get('(:num)', 'Reviews::show/ethics/$1');
    $routes->post('(:num)/draft', 'Reviews::draft/ethics/$1');
    $routes->post('(:num)/decision', 'Reviews::decide/ethics/$1');
    $routes->get('(:num)/download', 'Reviews::download/ethics/$1');
});
$routes->group('review/technical', ['filter' => 'role:technical_reviewer'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Reviews::index/technical');
    $routes->get('(:num)', 'Reviews::show/technical/$1');
    $routes->post('(:num)/draft', 'Reviews::draft/technical/$1');
    $routes->post('(:num)/decision', 'Reviews::decide/technical/$1');
    $routes->get('(:num)/download', 'Reviews::download/technical/$1');
});
$routes->group('admin', ['filter' => 'role:repository_administrator'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Admin::index');
    $routes->get('datasets', 'Admin::datasets');
    $routes->get('datasets/(:num)', 'Admin::dataset/$1');
    $routes->post('datasets/(:num)/assign', 'Admin::assign/$1');
    $routes->post('datasets/(:num)/reassign', 'Admin::reassign/$1');
    $routes->post('datasets/(:num)/publish', 'Admin::publish/$1');
    $routes->post('datasets/(:num)/archive', 'Admin::archive/$1');
    $routes->post('datasets/(:num)/restore', 'Admin::restore/$1');
    $routes->get('users', 'Admin::users');
    $routes->post('users', 'Admin::createUser');
    $routes->post('users/(:num)', 'Admin::updateUser/$1');
    $routes->get('audit-logs', 'Admin::auditLogs');
});

$routes->get('datasets', 'Datasets::index');
$routes->get('datasets/(:num)/cover', 'Datasets::cover/$1');
$routes->get('datasets/(:num)', 'Datasets::show/$1');
$routes->get('datasets/(:num)/download', 'Datasets::download/$1');
