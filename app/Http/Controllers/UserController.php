<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        return view('users.index', ['users' => $model->paginate(15)]);
    }

    /**
     * Show the specified user (read-only profile view).
     */
    public function show(User $user)
    {
        return view('profile.edit', ['user' => $user, 'readonly' => true]);
    }

    /**
     * Edit the specified user (admin can edit).
     */
    public function edit(User $user)
    {
        return view('profile.edit', ['user' => $user, 'readonly' => false]);
    }

    /**
     * Update the specified user.
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update($request->all());
        return redirect()->route('user.index')->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account from admin panel.']);
        }
        $user->delete();
        return redirect()->route('user.index')->with('status', 'User deleted successfully.');
    }
}
