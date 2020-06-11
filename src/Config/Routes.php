<?php

$routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_blog\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get('(:any)/blog/articles', 'AdminArticleController::renderViewList', ['as' => 'page-article']);
    $routes->get('(:any)/blog/articles/edit/(:any)', 'AdminArticleController::renderForm/$2');
    $routes->post('(:any)/blog/articles/edit/(:any)', 'AdminArticleController::postProcess/$2');
    $routes->get('(:any)/blog/articles/add', 'AdminArticleController::renderForm');
    $routes->post('(:any)/blog/articles/add', 'AdminArticleController::postProcess');
    $routes->get('(:any)/blog/articles/dupliquer/(:segment)', 'AdminArticleController::dupliquer/$2');

    $routes->get('(:any)/blog/categories', 'AdminCategorieController::renderViewList', ['as' => 'page-categorie']);
    $routes->get('(:any)/blog/categories/edit/(:any)', 'AdminCategorieController::renderForm/$2');
    $routes->post('(:any)/blog/categories/edit/(:any)', 'AdminCategorieController::postProcess/$2');
    $routes->get('(:any)/blog/categories/add', 'AdminCategorieController::renderForm');
    $routes->post('(:any)/blog/categories/add', 'AdminCategorieController::postProcess');

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


//Pages car le derneir de PSR4
$routes->get($locale . '/(:segment)' . env('app.suffix_url'), 'FrontPagesController::show/$1', ['namespace' => '\Adnduweb\Ci4_page\Controllers\Front']);
$routes->get($locale . '/(:segment)/(:segment)' . env('app.suffix_url'), 'FrontPagesController::show/$2', ['namespace' => '\Adnduweb\Ci4_page\Controllers\Front']);
$routes->get($locale . '/(:segment)/(:segment)/(:segment)' . env('app.suffix_url'), 'FrontPagesController::show/$3', ['namespace' => '\Adnduweb\Ci4_page\Controllers\Front']);
