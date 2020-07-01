<?php

$routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_blog\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get('(:any)/blog/posts', 'AdminPostsController::renderViewList', ['as' => 'page-post']);
    $routes->get('(:any)/blog/posts/edit/(:any)', 'AdminPostsController::renderForm/$2');
    $routes->post('(:any)/blog/posts/edit/(:any)', 'AdminPostsController::postProcess/$2');
    $routes->get('(:any)/blog/posts/add', 'AdminPostsController::renderForm');
    $routes->post('(:any)/blog/posts/add', 'AdminPostsController::postProcess');
    $routes->get('(:any)/blog/posts/dupliquer/(:segment)', 'AdminPostsController::dupliquer/$2');
    $routes->get('(:any)/blog/posts/fake/(:segment)', 'AdminPostsController::fake/$2');

    $routes->get('(:any)/blog/categories', 'AdminCategoriesController::renderViewList', ['as' => 'page-categorie']);
    $routes->get('(:any)/blog/categories/edit/(:any)', 'AdminCategoriesController::renderForm/$2');
    $routes->post('(:any)/blog/categories/edit/(:any)', 'AdminCategoriesController::postProcess/$2');
    $routes->get('(:any)/blog/categories/add', 'AdminCategoriesController::renderForm');
    $routes->post('(:any)/blog/categories/add', 'AdminCategoriesController::postProcess');

    $routes->get('(:any)/blog/settings', 'AdminBlogSettingsController::renderForm');
    $routes->post('(:any)/blog/settings', 'AdminBlogSettingsController::postProcess');
});

$locale = '/';
if (service('Settings')->setting_activer_multilangue == true) {
    $locale = '/{locale}';
}

//Blog
$routes->get($locale . '/' . env('url.blog_cat') . '/(:segment)' . env('app.suffix_url'), 'FrontCategoriesController::Show/$1', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front']);
$routes->get($locale . '/' . env('url.blog') . '/(:segment)' . env('app.suffix_url'), 'FrontArticleController::Show/$1', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front']);

$routes->get($locale . '/actualites' . env('app.suffix_url'), 'FrontCategoriesController::all', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front']);
$routes->get($locale . '/actualites/(:segment)' . env('app.suffix_url'), 'FrontArticleController::Show/$1', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front']);
$routes->get($locale . '/news' . env('app.suffix_url'), 'FrontCategoriesController::all', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front']);
$routes->get($locale . '/news/(:segment)' . env('app.suffix_url'), 'FrontArticleController::Show/$1', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front']);

//Pages car le dernier de PSR4
$routes->get($locale . '/(:segment)' . env('app.suffix_url'), 'FrontPagesController::show/$1', ['namespace' => '\Adnduweb\Ci4_page\Controllers\Front']);
$routes->get($locale . '/(:segment)/(:segment)' . env('app.suffix_url'), 'FrontPagesController::show/$2', ['namespace' => '\Adnduweb\Ci4_page\Controllers\Front']);
$routes->get($locale . '/(:segment)/(:segment)/(:segment)' . env('app.suffix_url'), 'FrontPagesController::show/$3', ['namespace' => '\Adnduweb\Ci4_page\Controllers\Front']);


