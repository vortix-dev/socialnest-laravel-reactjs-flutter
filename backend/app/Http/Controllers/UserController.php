<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $request->validate([
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:22480',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $file = $request->file('profile_image');
            $filename = uniqid($user->id.'_', true) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/profile_images', $filename);
            $imagePath = str_replace('public/', 'storage/', $path);

            $user->profile_img = $imagePath;
            $user->save();
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'image_url' => $user->profile_img ? asset($user->profile_img) : null,
        ]);
    }

}
