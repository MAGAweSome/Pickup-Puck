<?php

namespace App\Http\Controllers;

use App\Enums\Games\GameRoles;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;

class UserRoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(User $user)
    {
        return view('user_role', ['user' => $user, 'GAME_ROLES' => GameRoles::cases()]);
    }

    public function update(UserUpdateRequest $request, User $user) {        
        $user->name = $request['name'];
        $user->email = $request['email'];
        if ($request['gameRole'] != null) {
            $user->role_preference = $request['gameRole'];
        }

        if (isset($_POST['adminCheck'])){
            $user->assignRole('admin');
        }else{
            $user->removeRole('admin');
        }

        $user->save();


        return back()->with('success', 'You have successfully changed '.$user->name.'\'s profile!');
    }
}
