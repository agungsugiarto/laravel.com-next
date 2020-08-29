<?php

if (! defined('DEFAULT_VERSION')) {
    define('DEFAULT_VERSION', '7.x');
}

if (! defined('SHOW_VAPOR')) {
    define('SHOW_VAPOR', random_int(1, 2) === 1);
}

// laravel documentation
Route::get('docs', 'DocsController@showRootPage');

Route::get('docs/6.0/{page?}', function ($page = null) {
    $page = $page ?: 'installation';
    $page = $page == '7.x' ? 'installation' : $page;

    return redirect(trim('/docs/7.x/'.$page, '/'), 301);
});

Route::get('docs/{version}/{page?}', 'DocsController@show')->name('docs.version');

// lumen documentation
Route::get('lumen-docs', 'LumenDocsController@showRootPage');

Route::get('lumen-docs/6.0/{page?}', function ($page = null) {
    $page = $page ?: 'installation';
    $page = $page == '7.x' ? 'installation' : $page;

    return redirect(trim('/lumen-docs/7.x/'.$page, '/'), 301);
});

Route::get('lumen-docs/{version}/{page?}', 'LumenDocsController@show')->name('lumen.docs.version');

// utiliti

Route::get('partners', 'PartnersController@index');
Route::get('partner/{partner}', 'PartnersController@show');

Route::get('/', function () {
    return view('marketing');
});
