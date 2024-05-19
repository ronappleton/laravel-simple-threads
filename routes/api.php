<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix('threads')->group(function () {
    Route::get('/', 'ThreadController@index')->name('threads.index');
    Route::get('{thread}', 'ThreadController@show')->name('threads.show');
    Route::prefix('comment')->group(function () {
        Route::get('{comment}', 'CommentController@show')->name('threads.comment.show');
    });

    Route::prefix('report')->group(function () {
        Route::post('{thread}/thread', 'ThreadController@report')->name('threads.report');
        Route::post('{comment}/comment', 'CommentController@report')->name('threads.comment.report');
    });
});


Route::prefix('threads')->group(function () {
    Route::middleware(config()->array('threads.middleware', ['api', 'auth:api']))->group(function () {
        Route::post('/', 'ThreadController@store')->name('threads.store');
        Route::patch('{thread}', 'ThreadController@update')->name('threads.update');
        Route::delete('{thread}', 'ThreadController@destroy')->name('threads.destroy');
        Route::post('{thread}/like', 'ThreadController@toggleLike')->name('threads.like');
        Route::post('{thread}/lock', 'ThreadController@toggleLock')->name('threads.lock');
        Route::post('{thread}/pin', 'ThreadController@togglePin')->name('threads.pin');
        Route::post('{thread}/hide', 'ThreadController@toggleHide')->name('threads.hide');
        Route::post('{thread}/restore', 'ThreadController@restore')->name('threads.restore');
        Route::post('{thread}/comment', 'CommentController@store')->name('threads.comment.store');

        Route::prefix('comment')->group(function () {
            Route::patch('{comment}', 'CommentController@update')->name('threads.comment.update');
            Route::delete('{comment}', 'CommentController@destroy')->name('threads.comment.destroy');
            Route::post('{comment}/hide', 'CommentController@toggleHide')->name('threads.comment.hide');
            Route::post('{comment}/restore', 'CommentController@restore')->name('threads.comment.restore');
        });

        Route::prefix('report')->group(function () {
            Route::post('{threadReport}/resolve', 'ThreadController@resolveReport')->name('threads.report.resolve');
        });

        Route::prefix('block')->group(function () {
            Route::post('{user}/block', 'CommentController@blockCommenter')->name('threads.commenter.block');
            Route::post('{user}/unblock', 'CommentController@unBlockCommenter')->name('threads.commenter.unblock');
        });
    });
});
