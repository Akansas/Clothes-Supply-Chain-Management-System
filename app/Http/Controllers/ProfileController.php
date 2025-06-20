<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('profile.edit');
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        
        $data = $request->validated();
        
        // Debug: Log the validated data
        \Log::info('Profile update data:', $data);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Handle notification preferences
        if ($request->has('notification_preferences')) {
            $data['notification_preferences'] = [
                'email' => $request->boolean('notification_preferences.email', true),
                'push' => $request->boolean('notification_preferences.push', true),
                'chat' => $request->boolean('notification_preferences.chat', true),
            ];
        }

        // Debug: Log the final data to be updated
        \Log::info('Final update data:', $data);
        
        $result = $user->update($data);
        
        // Debug: Log the update result
        \Log::info('Update result:', ['success' => $result]);

        return back()->with('status', 'Profile successfully updated.');
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->with('password_status', 'Password successfully updated.');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notification_preferences.email' => 'boolean',
            'notification_preferences.push' => 'boolean',
            'notification_preferences.chat' => 'boolean',
        ]);

        $user = auth()->user();
        $user->notification_preferences = $request->notification_preferences;
        $user->save();

        return response()->json([
            'message' => 'Notification preferences updated successfully',
            'preferences' => $user->notification_preferences
        ]);
    }

    public function deleteAvatar()
    {
        $user = auth()->user();
        
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('status', 'Avatar successfully removed.');
    }
}
