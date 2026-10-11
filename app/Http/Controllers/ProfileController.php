<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function update(UpdateProfileRequest $request)
    {
        $userId = auth()->id(); // Lấy ID của người dùng hiện tại

        //lấy tất cả thông tin user có id = $userId
        $user = User::findOrFail($userId);
        $data = $request->all();

        $file = $request->avatar;

        if (!empty($file)) {

            $data['avatar'] = $file->getClientOriginalName();
        }

        if($data['password'])
            {

                $data['password'] = bcrypt($data['password']);
            }
            else
            {
                $data['password'] = $user->password;
            }

            if($user -> update($data)){

            if(!empty($file))
                {
                    $file -> move ('upload/user/avatar', $file->getClientOriginalName());

                }
                return redirect()->back()->with('success', 'Cập nhật thông tin thành công');

            }else{
                return redirect()->back()->with('error', 'Cập nhật thông tin thất bại');
            }


    }
}
