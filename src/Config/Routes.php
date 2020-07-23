<?php

$routes->group(CI_SITE_AREA, ['namespace' => 'Adnduweb\Ci4_blog\Controllers\Admin', 'filter' => 'apiauth'], function ($routes) {

    $routes->get(config('Blog')->urlMenuAdmin . '/blog/posts', 'AdminPostController::renderViewList', ['as' => 'blog-post']);
    $routes->get(config('Blog')->urlMenuAdmin . '/blog/posts/edit/(:any)', 'AdminPostController::renderForm/$1');
    $routes->post(config('Blog')->urlMenuAdmin . '/blog/posts/edit/(:any)', 'AdminPostController::postProcess/$1');
    $routes->get(config('Blog')->urlMenuAdmin . '/blog/posts/add', 'AdminPostController::renderForm');
    $routes->post(config('Blog')->urlMenuAdmin . '/blog/posts/add', 'AdminPostController::postProcess');
    $routes->get(config('Blog')->urlMenuAdmin . '/blog/posts/dupliquer/(:segment)', 'AdminPostController::dupliquer/$1');
    $routes->get(config('Blog')->urlMenuAdmin . '/blog/posts/fake/(:segment)', 'AdminPostController::fake/$1');

    $routes->get(config('Blog')->urlMenuAdmin . '/blog/categories', 'AdminCategoryController::renderViewList', ['as' => 'blog-categorie']);
    $routes->get(config('Blog')->urlMenuAdmin . '/blog/categories/edit/(:any)', 'AdminCategoryController::renderForm/$1');
    $routes->post(config('Blog')->urlMenuAdmin . '/blog/categories/edit/(:any)', 'AdminCategoryController::postProcess/$1');
    $routes->get(config('Blog')->urlMenuAdmin . '/blog/categories/add', 'AdminCategoryController::renderForm');
    $routes->post(config('Blog')->urlMenuAdmin . '/blog/categories/add', 'AdminCategoryController::postProcess');

    $routes->get(config('Blog')->urlMenuAdmin . '/blog/settings', 'AdminBlogSettingsController::renderForm');
    $routes->post(config('Blog')->urlMenuAdmin . '/blog/settings', 'AdminBlogSettingsController::postProcess');
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


