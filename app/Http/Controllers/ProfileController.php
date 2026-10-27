<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class ProfileController extends Controller
{
    //
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->except(['avatar', 'password']);
        $oldAvatarPath = $user->avatar;

        if ($request->filled('password')) {
            $data['password'] = $request->validated('password');
        }

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('upload/user/avatar', 'public');

            if (! $avatarPath) {
                throw new RuntimeException('Unable to store the uploaded avatar.');
            }

            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        if ($request->hasFile('avatar')
            && $oldAvatarPath !== null
            && str_contains($oldAvatarPath, '/')
            && Storage::disk('public')->exists($oldAvatarPath)
            && ! Storage::disk('public')->delete($oldAvatarPath)) {
            throw new RuntimeException('Unable to remove the previous profile avatar.');
        }

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công.');
    }

    public function editCountry($id)
    {
        $country = Country::findOrFail($id);

        return view('admin.country.edit', compact('country'));
    }

    public function edit(Request $request): View
    {
        return view('frontend.account.profile', [
            'user' => $request->user(),
            'countries' => Country::query()->orderBy('name')->get(),
        ]);
    }
}
