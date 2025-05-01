<?php 

namespace App\Actions\Fortify;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class AdminLoginResponse implements LoginResponseContract{
    public function toResponse($request)
    {
        if(auth('admin')->check()){
            return redirect('/admin/attendance/list');
        }

        return redirect('/attendance');
    }
}