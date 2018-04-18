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
    Route::resource('shifts', 'ShiftController');
    Route::resource('locations', 'LocationController');
    Route::post('employeesImport', 'EmployeeController@importExcel')->name('employees.importExcel');
    Route::resource('employees', 'EmployeeController');

    Route::get('/assignEmpShiftAttendance', 'DashboardController@assignEmpShiftAttendance')->name('assignEmpShiftAttendance');
    Route::get('/getShift', 'DashboardController@getShift')->name('getShift');
    
    Route::get('/shiftDetails', 'DashboardController@shiftDetails')->name('shiftDetails');
    Route::get('/shiftDetailsChange', 'DashboardController@shiftDetailsChange')->name('shiftDetailsChange');
    Route::get('/employeeSearch', 'DashboardController@employeeSearch')->name('employeeSearch');
    Route::get('/employeeAdd', 'DashboardController@employeeAdd')->name('employeeAdd');
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

Route::group(['prefix' => 'dept', 'as' => 'dept.', 'namespace' => 'Dept', 'middleware' => 'dept'], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/shift', 'DashboardController@shift')->name('shift');
    Route::get('/shiftStatus', 'DashboardController@shiftBatch')->name('shiftBatch');
    Route::post('/assignShift', 'DashboardController@assignShift')->name('assignShift');
    Route::get('/assignShiftCheck', 'DashboardController@assignShiftCheck')->name('assignShiftCheck');
    Route::get('/assignEmpShiftCheck', 'DashboardController@assignEmpShiftCheck')->name('assignEmpShiftCheck');
    Route::get('/assignEmpShiftIndividual', 'DashboardController@assignEmpShiftIndividual')->name('assignEmpShiftIndividual');
    Route::get('/bulkSelect', 'DashboardController@bulkSelect')->name('bulkSelect');
    Route::get('/shiftList', 'DashboardController@shiftList')->name('shiftList');
    Route::get('/shiftDetails', 'DashboardController@shiftDetails')->name('shiftDetails');
    Route::get('/shiftDetailsChange', 'DashboardController@shiftDetailsChange')->name('shiftDetailsChange');
    Route::get('/employeeSearch', 'DashboardController@employeeSearch')->name('employeeSearch');
    Route::get('/employeeAdd', 'DashboardController@employeeAdd')->name('employeeAdd');

    Route::get('/employeeReassignList', 'DashboardController@employeeReassignList')->name('employeeReassignList');

    Route::get('/employeeReassign', 'DashboardController@employeeReassign')->name('employeeReassign');

    Route::post('/employeeReassignStore', 'DashboardController@employeeReassignStore')->name('employeeReassignStore');

    Route::get('/employeeBatchSearch', 'DashboardController@employeeBatchSearch')->name('employeeBatchSearch');

    Route::get('/otherDept', 'DashboardController@otherDept')->name('otherDept');

    Route::get('/assignOtherDep', 'DashboardController@assignOtherDep')->name('assignOtherDep');
    
    Route::get('/shiftBulkDetailsChange', 'DashboardController@shiftBulkDetailsChange')->name('shiftBulkDetailsChange');

    Route::get('/holidayShift', 'DashboardController@holidayShift')->name('holidayShift');

    Route::get('/holidayShiftAssign', 'DashboardController@holidayShiftAssign')->name('holidayShiftAssign');

});

Route::group(['prefix' => 'hr', 'as' => 'hr.', 'namespace' => 'Hr', 'middleware' => 'hr'], function () {
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/shift', 'DashboardController@shift')->name('shift');
    Route::get('/shiftStatus', 'DashboardController@shiftBatch')->name('shiftBatch');
    Route::post('/assignShift', 'DashboardController@assignShift')->name('assignShift');
    Route::get('/assignEmpShiftCheck', 'DashboardController@assignEmpShiftCheck')->name('assignEmpShiftCheck');
    Route::get('/assignEmpShiftAttendance', 'DashboardController@assignEmpShiftAttendance')->name('assignEmpShiftAttendance');
    Route::get('/getShift', 'DashboardController@getShift')->name('getShift');

    Route::get('/shiftDetails', 'DashboardController@shiftDetails')->name('shiftDetails');
    Route::get('/shiftDetailsChange', 'DashboardController@shiftDetailsChange')->name('shiftDetailsChange');
    Route::get('/employeeSearch', 'DashboardController@employeeSearch')->name('employeeSearch');
    Route::get('/employeeAdd', 'DashboardController@employeeAdd')->name('employeeAdd');
    Route::post('/bulkConfirmedShift', 'DashboardController@bulkConfirmedShift')->name('bulkConfirmedShift');

    Route::get('/report', 'DashboardController@reportPage')->name('reportPage');
    Route::get('/reportEmployee', 'DashboardController@reportEmployeePage')->name('reportEmployeePage');
    Route::get('/getReport', 'DashboardController@getReport')->name('getReport');

    Route::get('/getDepartmentEmployee', 'DashboardController@getDepartmentEmployee')->name('getDepartmentEmployee');

    Route::get('/shiftBulkDetailsChange', 'DashboardController@shiftBulkDetailsChange')->name('shiftBulkDetailsChange');

    Route::get('/holidayBatch', 'DashboardController@holidayBatch')->name('holidayBatch');

    Route::get('/holidayShift', 'DashboardController@holidayShift')->name('holidayShift');

    Route::get('/holidayShiftAssign', 'DashboardController@holidayShiftAssign')->name('holidayShiftAssign');
});