<?php

use App\Http\Controllers\CreateGameController;
use App\Http\Controllers\EditGameController;
use App\Http\Controllers\GameDetailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpdatePasswordController;
use App\Http\Controllers\UpdateProfileController;
use App\Http\Controllers\UserGameHistoryController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\Auth\ForgotPasswordsController;
use App\Http\Controllers\Auth\ResetPasswordController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route to the first page of the website
Route::get('/', function () {
    // If the user wants to be sent to the main page
    // return view('index');

    if ( Auth::user() )
        // If the user is already logged in, go to the home page
        return redirect('home');
    else
        // If the user is not logged in, have the main page, the login page
        return view('auth.login');
});

Auth::routes(['verify' => true]);

Route::middleware('verified')->group(function () {
    // Must have a verified account to access
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile',[ProfileController::class, 'update'])->name('profile_update');
    Route::get('/update_profile', [UpdateProfileController::class, 'index'])->name('update_profile');
    Route::get('/update/password',[UpdatePasswordController::class, 'index'])->name('update_password');

    Route::get('/game/{game}', [GameDetailController::class, 'index'])->name('game_detail.game_id');
    Route::get('/game/{game}/search', [GameDetailController::class, 'searchGuestList'])->name('game_detail_search_guest.game_id');
    Route::post('/game/{game}/role', [GameDetailController::class, 'update'])->name('game_detail_update.game_id');
    Route::post('/game/{game}/name/role', [GameDetailController::class, 'updateGuest'])->name('game_detail_update_guest.game_id');
    Route::post('/admin/game/{game}/{user_id}/role', [GameDetailController::class, 'adminUpdate'])->name('admin_game_detail_update.game_id.user_id');
    Route::post('/game/{game}/payment', [GameDetailController::class, 'payment'])->name('game_detail_pay.game_id');
    Route::post('/admin/game/{game}/{player_id}/payment', [GameDetailController::class, 'adminPayment'])->name('admin_game_detail_pay.game_id.player_id');
    Route::get('/game/{game}/generateTeams', [GameDetailController::class, 'generateTeams'])->name('game_detail_generateTeams.game_id');
    Route::get('clear_cache', function () {

        \Illuminate\Support\Facades\Artisan::call('pp:generate-teams');
    
    });

    // Must have admin role to access
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::get('/user', [UserListController::class, 'index'])->name('user_list');
        Route::get('/user/{user}/history', [UserGameHistoryController::class, 'index'])->name('user_game_history');
        Route::get('/user/{user}', [UserRoleController::class, 'index'])->name('user_role.user_id');
        Route::post('/user/{user}',[UserRoleController::class, 'update'])->name('user_role_update.user_id');
        Route::get('/create_game', [CreateGameController::class, 'index'])->name('create_game');
        Route::post('/create_game', [CreateGameController::class, 'create'])->name('game_create');
        Route::get('/edit_game/{game}', [EditGameController::class, 'index'])->name('edit_game');
        Route::post('/edit_game/{game}', [EditGameController::class, 'update'])->name('game_edit');
    });
});

    