<?php


$routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_blog\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get('(:num)/(:any)/blog/articles', 'AdminArticleController::renderViewList', ['as' => 'page-article']);
    $routes->get('(:num)/(:any)/blog/articles/edit/(:any)', 'AdminArticleController::renderForm/$3');
    $routes->post('(:num)/(:any)/blog/articles/edit/(:any)', 'AdminArticleController::postProcess/$3');
    $routes->get('(:num)/(:any)/blog/articles/add', 'AdminArticleController::renderForm');
    $routes->post('(:num)/(:any)/blog/articles/add', 'AdminArticleController::postProcess');
});

