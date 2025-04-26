<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        if (auth()->user()->user_type_id == 2) {
            Log::info('User is admin. Redirecting to admin dashboard.', ['user_id' => auth()->id(), 'user_type_id' => auth()->user()->user_type_id]);
            return '/admin'; // เปลี่ยนเส้นทางไปหน้าแดชบอร์ดของแอดมิน
        }

        Log::info('User is not admin. Redirecting to user dashboard.', ['user_id' => auth()->id(), 'user_type_id' => auth()->user()->user_type_id]);
        return '/dashboard'; // เปลี่ยนเส้นทางไปหน้าแดชบอร์ดของผู้ใช้ทั่วไป
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Get the failed login validation response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
            ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
            $this->username().'.required' => 'กรุณากรอกชื่อผู้ใช้',
            $this->username().'.string' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษรหรือตัวเลขเท่านั้น',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.string' => 'รหัสผ่านต้องเป็นตัวอักษรเท่านั้น',
        ]);
    }
}
