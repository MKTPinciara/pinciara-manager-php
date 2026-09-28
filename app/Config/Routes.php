<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Rota raiz redireciona para a esteira de imóveis
$routes->get('/', function () {
    return redirect()->to('/imoveis');
});

// Autenticação (Públicas)
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Rotas Protegidas (Exigem Login)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Esteira de Imóveis
    $routes->get('imoveis', 'Imoveis::index');
    $routes->post('imoveis', 'Imoveis::store');
    $routes->post('imoveis/update/(:num)', 'Imoveis::update/$1');
    $routes->post('imoveis/status/(:num)', 'Imoveis::updateStatus/$1');
    $routes->post('imoveis/order', 'Imoveis::updateOrder');
    $routes->match(['get', 'post'], 'imoveis/delete/(:num)', 'Imoveis::delete/$1');

    // Gestão de Placas
    $routes->get('placas', 'Placas::index');
    $routes->post('placas', 'Placas::store');
    $routes->post('placas/update/(:num)', 'Placas::update/$1');
    $routes->post('placas/status/(:num)', 'Placas::updateStatus/$1');
    $routes->match(['get', 'post'], 'placas/delete/(:num)', 'Placas::delete/$1');
});
