<?php

use App\Http\Controllers\CreateGameController;
use App\Http\Controllers\EditGameController;
use App\Http\Controllers\GameDetailController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpdatePasswordController;
use App\Http\Controllers\UpdateProfileController;
use App\Http\Controllers\UserGameHistoryController;
use App\Http\Controllers\GuestGameHistoryController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SetupController;
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
        // If the user is not logged in, show the showcase landing page
        return view('landing');
});

Auth::routes(['verify' => true]);

// AJAX endpoint for registration email availability (usable by guests)
Route::post('/register/check-email', [App\Http\Controllers\Auth\RegisterController::class, 'checkEmail'])->name('register.check_email');

// Public calendar download (.ics) for external calendar apps and mobile clients
Route::get('/game/{game}/calendar/ics', [GameDetailController::class, 'downloadIcs'])->name('game.calendar.ics');

// Real-time verification status check for auto-refresh
Route::middleware('auth')->get('/email/verification-status', function () {
    return response()->json([
        'verified' => auth()->user() ? auth()->user()->hasVerifiedEmail() : false,
    ]);
})->name('verification.status');

// Route::middleware('verified')->group(function () {
// Must have a verified account to access

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile',[ProfileController::class, 'update'])->name('profile_update');
    Route::post('/profile/password', [UpdatePasswordController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/update_profile', [UpdateProfileController::class, 'index'])->name('update_profile');
    Route::get('/update/password',[UpdatePasswordController::class, 'index'])->name('update_password');


    // Structural pages (sidebar links)
    Route::get('/games', [GameController::class, 'index'])->name('games.index');

    // Onboarding (regular users)
    Route::get('/onboarding/game-details', [OnboardingController::class, 'gameDetailsDemo'])->name('onboarding.game-details');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');

    // One-time setup: elevate current user to admin
    Route::get('/setup/elevate-me-puck-admin', [SetupController::class, 'showElevateMePuckAdmin'])
        ->middleware('throttle:10,1')
        ->name('setup.elevate-me-puck-admin');
    Route::post('/setup/elevate-me-puck-admin', [SetupController::class, 'elevateMePuckAdmin'])
        ->middleware('throttle:10,1')
        ->name('setup.elevate-me-puck-admin.submit');

    Route::get('/payments', function () {
        return view('payments.index');
    })->name('payments.index');

    Route::get('/seasons', function () {
        return view('seasons.index');
    })->name('seasons.index');

    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/seasons/{season}/accept-all', [HomeController::class, 'acceptAllGamesInSeason'])->name('seasons.accept-all');

    Route::get('/game/{game}', [GameDetailController::class, 'index'])->name('game_detail.game_id');
    Route::get('/game/{game}/teams-roster', [GameDetailController::class, 'teamsRoster'])->name('game.teams_roster');
    Route::get('/game/{game}/search', [GameDetailController::class, 'searchGuestList'])->name('game_detail_search_guest.game_id');
    Route::post('/game/{game}/role', [GameDetailController::class, 'update'])->name('game_detail_update.game_id');
    Route::post('/game/{game}/cannot-attend', [GameDetailController::class, 'cannotAttend'])->name('game_detail_cannot_attend');
    // Allow a user to remove themselves from a game
    Route::post('/game/{game}/remove', [GameDetailController::class, 'removeSelf'])->name('game_remove_self');
    Route::post('/game/{game}/name/role', [GameDetailController::class, 'updateGuest'])->name('game_detail_update_guest.game_id');
    Route::post('/admin/game/{game}/{user_id}/role', [GameDetailController::class, 'adminUpdate'])
        ->middleware(['role:admin'])
        ->name('admin_game_detail_update.game_id.user_id');
    Route::post('/admin/game/{game}/teams/move', [GameDetailController::class, 'adminMoveTeamMember'])
        ->middleware(['role:admin'])
        ->name('admin_game_team_move');
    Route::post('/admin/game/{game}/{user_id}/remove', [GameDetailController::class, 'adminRemovePlayer'])
        ->middleware(['role:admin'])
        ->name('admin_game_detail_remove_player');
    Route::post('/game/{game}/payment', [GameDetailController::class, 'payment'])->name('game_detail_pay.game_id');
    Route::post('/admin/game/{game}/{player_id}/payment', [GameDetailController::class, 'adminPayment'])
        ->middleware(['role:admin'])
        ->name('admin_game_detail_pay.game_id.player_id');
    Route::get('/game/{game}/generateTeams', [GameDetailController::class, 'generateTeams'])->name('game_detail_generateTeams.game_id');
        Route::post('/game/{game}/teams/remove-assignment', [GameDetailController::class, 'adminRemoveTeamAssignment'])
            ->middleware(['role:admin'])
            ->name('admin_game_team_remove_assignment');
    // Admin guest management (admin can change guest role or remove guest)
    Route::post('/admin/game/{game}/guest/{guest_id}/role', [GameDetailController::class, 'adminUpdateGuest'])
        ->middleware(['role:admin'])
        ->name('admin_game_detail_update_guest');
    Route::post('/admin/game/{game}/guest/{guest_id}/remove', [GameDetailController::class, 'adminRemoveGuest'])
        ->middleware(['role:admin'])
        ->name('admin_game_detail_remove_guest');
    Route::get('clear_cache', function () {

        \Illuminate\Support\Facades\Artisan::call('pp:generate-teams');
    
    });

    // Must have admin role to access
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::get('/user', [UserListController::class, 'index'])->name('user_list');
        Route::get('/user/{user}/history', [UserGameHistoryController::class, 'index'])->name('user_game_history');
        Route::get('/guest/{guest}/history', [GuestGameHistoryController::class, 'index'])->name('guest_game_history');
        Route::get('/user/{user}', [UserRoleController::class, 'index'])->name('user_role.user_id');
        Route::post('/user/{user}',[UserRoleController::class, 'update'])->name('user_role_update.user_id');
        Route::get('/create_game', [CreateGameController::class, 'index'])->name('create_game');
        Route::post('/create_game', [CreateGameController::class, 'create'])->name('game_create');
        Route::post('/seasons', [CreateGameController::class, 'createSeason'])->name('season.create');
        Route::delete('/seasons/{season}', [App\Http\Controllers\SettingsController::class, 'deleteSeason'])->name('admin.seasons.delete');
        Route::post('/seasons/{season}/delete', [App\Http\Controllers\SettingsController::class, 'deleteSeason'])->name('admin.seasons.delete_post');
        Route::get('/edit_game/{game}', [EditGameController::class, 'index'])->name('edit_game');
        Route::post('/edit_game/{game}', [EditGameController::class, 'update'])->name('game_edit');
        Route::post('/game/{game}/score', [App\Http\Controllers\GameDetailController::class, 'adminUpdateScore'])->name('admin_game_update_score');
        Route::get('/delete_game/{game}', [EditGameController::class, 'delete'])->name('delete_game');
    });
});

// Email Template Previews (for visual design inspection in local/dev)
Route::get('/email-preview/reset-password', function () {
    $dummyUser = (object) [
        'name' => auth()->check() ? auth()->user()->name : 'Wayne Gretzky',
        'email' => auth()->check() ? auth()->user()->email : 'wayne@example.com',
    ];
    $dummyUrl = url('/password/reset/sample-token-12345?email=' . urlencode($dummyUser->email));

    return view('emails.auth.reset-password', [
        'user' => $dummyUser,
        'url' => $dummyUrl,
        'count' => 60,
    ]);
})->name('email_preview.reset_password');

Route::get('/email-preview/verify-email', function () {
    $dummyUser = (object) [
        'name' => auth()->check() ? auth()->user()->name : 'Connor McDavid',
        'email' => auth()->check() ? auth()->user()->email : 'connor@example.com',
    ];
    $dummyUrl = url('/email/verify/sample-id/sample-hash');

    return view('emails.auth.verify-email', [
        'user' => $dummyUser,
        'url' => $dummyUrl,
    ]);
})->name('email_preview.verify_email');


    