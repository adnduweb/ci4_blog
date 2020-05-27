<?php

$routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_blog\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get('(:any)/blog/articles', 'AdminArticleController::renderViewList', ['as' => 'page-article']);
    $routes->get('(:any)/blog/articles/edit/(:any)', 'AdminArticleController::renderForm/$2');
    $routes->post('(:any)/blog/articles/edit/(:any)', 'AdminArticleController::postProcess/$2');
    $routes->get('(:any)/blog/articles/add', 'AdminArticleController::renderForm');
    $routes->post('(:any)/blog/articles/add', 'AdminArticleController::postProcess');

    $routes->get('(:any)/blog/categories', 'AdminCategorieController::renderViewList', ['as' => 'page-categorie']);
    $routes->get('(:any)/blog/categories/edit/(:any)', 'AdminCategorieController::renderForm/$2');
    $routes->post('(:any)/blog/categories/edit/(:any)', 'AdminCategorieController::postProcess/$2');
    $routes->get('(:any)/blog/categories/add', 'AdminCategorieController::renderForm');
    $routes->post('(:any)/blog/categories/add', 'AdminCategorieController::postProcess');

    $routes->get('public/blog/settings', 'AdminBlogSettingsController::renderForm');
    $routes->post('public/blog/settings', 'AdminBlogSettingsController::postProcess');
});
