<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProfileController extends Controller
{
    //
    public function update(UpdateProfileRequest $request)
    {
        $user = User::findOrFail($request->user()->id);
        $data = $request->safe()->except(['avatar', 'password']);

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

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công');
    }

    public function editCountry($id)
    {
        $country = Country::findOrFail($id);
        return view ('admin.country.edit', compact('country'));
    }
}
