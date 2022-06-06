<?php

use App\Http\Controllers\ArticleFileController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
header('Access-Control-Allow-Origin: *');
header( 'Access-Control-Allow-Headers: Authorization, Content-Type' );
Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('article', 'ArticleController');
    Route::get('journals/dates', 'JournalController@dates');
    Route::get('auth_user/journals/editor', 'AuthUserController@journalsEditor');
    Route::resource('article.file.antiplagiat_report', 'AntiplagiatReportController', [
        'parameters' => [
            'file' => 'article_file'
        ]
    ]);

    Route::resource('article.file', 'ArticleFileController', [
        'parameters' => [
            'file' => 'article_file'
        ]
    ]);

    Route::get('journals/report/sum', 'JournalReportController@sum');
    Route::get('journals/report/articles', 'JournalReportController@articles');

});

