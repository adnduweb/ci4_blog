<?php

$routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_blog\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get('(:num)/(:any)/blog/articles', 'AdminArticleController::renderViewList', ['as' => 'page-article']);
    $routes->get('(:num)/(:any)/blog/articles/edit/(:any)', 'AdminArticleController::renderForm/$3');
    $routes->post('(:num)/(:any)/blog/articles/edit/(:any)', 'AdminArticleController::postProcess/$3');
    $routes->get('(:num)/(:any)/blog/articles/add', 'AdminArticleController::renderForm');
    $routes->post('(:num)/(:any)/blog/articles/add', 'AdminArticleController::postProcess');

    $routes->get('(:num)/(:any)/blog/categories', 'AdminCategorieController::renderViewList', ['as' => 'page-categorie']);
    $routes->get('(:num)/(:any)/blog/categories/edit/(:any)', 'AdminCategorieController::renderForm/$3');
    $routes->post('(:num)/(:any)/blog/categories/edit/(:any)', 'AdminCategorieController::postProcess/$3');
    $routes->get('(:num)/(:any)/blog/categories/add', 'AdminCategorieController::renderForm');
    $routes->post('(:num)/(:any)/blog/categories/add', 'AdminCategorieController::postProcess');

    $routes->get('(:num)/public/blog/settings', 'AdminBlogSettingsController::renderForm');
    $routes->post('(:num)/public/blog/settings', 'AdminBlogSettingsController::postProcess');
});


$routes->group('', ['namespace' => '\Adnduweb\Ci4_blog\Controllers\Front'], function ($routes) {

    $locale = '/';
    if (service('Settings')->setting_activer_multilangue == true) {
        $locale = '/{locale}';
    }
     $routes->get($locale . '/categories/(:any)', 'CategoriesController::View/$1');
     $routes->get($locale . '/actualites/(:any)', 'ArticleController::View/$1');
});
