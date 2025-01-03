<?php

use App\Http\Controllers\Backend\ScormController;
use App\Http\Controllers\Backend\ContentManagementController;
use App\Http\Controllers\Backend\FileUploadController;
use App\Http\Controllers\Backend\ServerMonitorController;
use App\Http\Controllers\Backend\NotificationHandlerController;
use App\Http\Controllers\Backend\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');




/**
 * Admin routes
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', 'Backend\DashboardController@index')->name('admin.dashboard');
    Route::resource('roles', 'Backend\RolesController', ['names' => 'admin.roles']);
    Route::resource('users', 'Backend\UsersController', ['names' => 'admin.users']);
    Route::resource('admins', 'Backend\AdminsController', ['names' => 'admin.admins']);


    // Login Routes
    Route::get('/login', 'Backend\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('/login/submit', 'Backend\Auth\LoginController@login')->name('admin.login.submit');

    // Logout Routes
    Route::post('/logout/submit', 'Backend\Auth\LoginController@logout')->name('admin.logout.submit');

    // Forget Password Routes
    // Route::get('/password/reset', 'Backend\Auth\ForgetPasswordController@showLinkRequestForm')->name('admin.password.request');
    // Route::post('/password/reset/submit', 'Backend\Auth\ForgetPasswordController@reset')->name('admin.password.update');

    // Forget Password Routes
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('/password/reset/submit', [ForgotPasswordController::class, 'reset'])->name('admin.password.update'); // Change this method to send the email


    // Route::get('/content-management', [ContentManagementController::class,'index'])->name('content.index');
    // Route::post('/store-content', [ContentManagementController::class,'store']);
    // Route::delete('/content/{id}', [ContentManagementController::class, 'destroy'])->name('content.destroy');
    // Route::post('/temp-upload', [ContentManagementController::class,'tmpUpload']);
    // Route::delete('/temp-delete', [ContentManagementController::class,'tmpDelete']);

    // Route::get('file-upload', [FileUploadController::class, 'index'])->name('files.index');
    // Route::post('admin/upload-file', [FileUploadController::class, 'uploadLarge'])->name('files.upload.large');

    Route::get('/content-management', [FileUploadController::class,'index'])->name('content.index');
    Route::get('upload', [FileUploadController::class, 'index'])->name('upload.index');
    Route::post('upload', [FileUploadController::class, 'store'])->name('upload.store');
    Route::post('/upload/save-to-bucket', [FileUploadController::class, 'saveFileToBucket'])->name('upload.saveBucket');
    Route::delete('/upload/delete', [FileUploadController::class, 'tmpDelete'])->name('upload.delete');
    Route::delete('/upload/{id}', [FileUploadController::class, 'destroy'])->name('upload.destroy');
    Route::get('/content-log/{id}', [FileUploadController::class, 'showLog'])->name('content-log.show'); // View specific server
    // Route::get('/log/show', [FileUploadController::class, 'showLog'])->name('content-log.show'); 

    //new contentmanagement

    Route::get('/content/create', [FileUploadController::class, 'create'])->name('content.create');

    
    Route::get('server-monitor', [ServerMonitorController::class, 'index'])->name('server-monitor.index');
    Route::get('server-monitor/create', [ServerMonitorController::class, 'create'])->name('server-monitor.create');
    Route::post('server-monitor/store', [ServerMonitorController::class, 'store'])->name('server-monitor.store');
    Route::get('server-monitor/edit/{id}', [ServerMonitorController::class, 'edit'])->name('server-monitor.edit');
    Route::post('server-monitor/update/{id}', [ServerMonitorController::class, 'update'])->name('server-monitor.update');
    Route::delete('server-monitor/delete/{id}', [ServerMonitorController::class, 'destroy'])->name('server-monitor.destroy');
    Route::get('/server-monitor/{id}', [ServerMonitorController::class, 'show'])->name('server-monitor.show'); // View specific server

    Route::resource('notification-handler', NotificationHandlerController::class);

});

Route::get('/server-monitor-code/{id}/{api_key}', [ServerMonitorController::class, 'serveMonitorScript']);



// Login Routes
// Route::get('/user/login', 'frontend\Auth\LoginController@showLoginForm')->name('user.login');
// Route::post('/user/login/submit', 'frontend\Auth\LoginController@login')->name('user.login.submit');

// // Logout Routes
// Route::post('/user/logout/submit', 'frontend\Auth\LoginController@logout')->name('user.logout.submit');

// Forget Password Routes
// Route::get('/user/password/reset', 'frontend\Auth\ForgetPasswordController@showLinkRequestForm')->name('user.password.request');
// Route::post('/user/password/reset/submit', 'frontend\Auth\ForgetPasswordController@reset')->name('user.password.update');