<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth'], function () {

    // Activity Logs
    Route::get('/activity-logs', 'ActivityController@index')->name('activity-logs.index');
    Route::delete('/activity-logs/clear', 'ActivityController@clear')->name('activity-logs.clear');
    Route::get('/activity-logs/{activity}', 'ActivityController@show')->name('activity-logs.show');
    Route::delete('/activity-logs/{activity}', 'ActivityController@destroy')->name('activity-logs.destroy');

});
