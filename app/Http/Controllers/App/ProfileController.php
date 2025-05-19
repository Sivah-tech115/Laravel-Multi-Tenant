<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        if ($request->has('changeprofile')) {
            // 👉 Handle profile info update
            $request->validate([
                'fname' => 'required|string|max:255',
                'email' => 'required|email',
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
            ]);

            $user = auth()->user();
            $user->name = $request->fname;
            $user->email = $request->email;

            if ($request->hasFile('image')) {
                $filename = time() . '.' . $request->image->extension();
            
                // Store the file in 'public/image' folder
                $request->image->storeAs('image', $filename, 'public');
            
                // Save the relative path to the image (e.g., image/123456789.jpg)
                $user->image = 'image/' . $filename;
            }

            $user->save();

            return back()->with('success', 'Profile updated successfully!');
        }

        if ($request->has('changepassbtn')) {
            // 👉 Handle password update
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = auth()->user();
            $user->password = bcrypt($request->password);
            $user->save();

            return back()->with('success', 'Password updated successfully!');
        }

        return back()->with('error', 'Invalid form submission.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
