<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function destroyUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->respondError(null, 'User not found');
        }
        $user->delete();
        return $this->respondOk(null, 'User deleted successfully');
    }

    public function banUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required:exists:users,id',
            'banned_until' => 'required|date|after:now',
        ]);

        $user = User::find($request->user_id);

        $user->banned_until = $request->banned_until;
        $user->save();

        return $this->respondOk(null, 'User banned until : ' . $request->banned_until);
    }

    public function removeBan($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->respondError('User not found');
        }

        $user->banned_until = null;
        $user->save();

        return $this->respondOk(null, 'User ban removed successfully');
    }

    public function getAllUsers()
    {
        $users = User::paginate();
        return $this->respondOk($users, 'Users retrieved successfully');
    }
}
