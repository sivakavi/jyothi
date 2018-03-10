<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/**
 * Auth routes
 */
Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    // Confirmation Routes...
    if (config('auth.users.confirm_email')) {
        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');
        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');
    }
});

/**
 * Backend routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'admin'], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');

    //Users
    Route::get('users', 'UserController@index')->name('users');
    Route::get('users/create', 'UserController@create')->name('users.create');
    Route::post('users', 'UserController@store')->name('users.store');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::put('users/{user}', 'UserController@update')->name('users.update');
    
    Route::delete('users/{user}', 'UserController@destroy')->name('users.destroy');
    Route::resource("departments","DepartmentController");
    Route::resource("categories","CategoryController");
    Route::resource('leaves', 'LeaveController');
    Route::resource('statuses', 'StatusController');
    Route::resource('work_types', 'WorkTypeController');
    Route::get('shift', 'UserController@index')->name('shift');
    Route::get('employee', 'UserController@index')->name('employee');
});


Route::get('/', 'HomeController@index');

Route::post('users/change-password', 'UserController@postCredentials')->name('users.change-password');

Route::get('change-password', 'UserController@changePassword')->name('users.changePassword');

Route::get('profile', 'UserController@profile')->name('users.profile');



/**
* Student routes
*/



Route::group(['prefix' => 'student', 'as' => 'student.', 'namespace' => 'Student', 'middleware' => 'student'], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/category/{id}', 'DashboardController@category')->name('category');
    Route::get('/subCategories', 'DashboardController@subCategories')->name('sub.categories');

    Route::get('/subCategory/{id}', 'DashboardController@subCategory')->name('sub.category');

    Route::get('/subCategory/pdf/{id}', 'DashboardController@subCategoryPDF')->name('view.pdf');

    Route::get('/test/{id}', 'DashboardController@test')->name('test');
});

Route::group(['prefix' => 'staff', 'as' => 'staff.', 'namespace' => 'Staff', 'middleware' => 'staff'], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/student-list', 'DashboardController@studentList')->name('studentLists');
    Route::get('/student/{id}', 'DashboardController@student')->name('studentShow');
});

Route::get('/hr-dashboard', 'DashboardController@testHr')->name('testHr');
Route::get('/dept-dashboard', 'DashboardController@testDept')->name('testDept');
