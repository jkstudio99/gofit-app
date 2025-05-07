<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        // ตรวจสอบว่ามี validation error หรือไม่
        if ($request->has($this->username()) && $request->has('password')) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    $this->username() => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
                ]);
        }

        // ถ้าไม่มีข้อมูลที่กรอก ให้แสดง validation error แทน
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'));
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
        // หากไม่มีการกรอกข้อมูลหรือข้อมูลว่าง ให้ทำการ validate แบบปกติ
        // (จะแสดง validation errors กรณีไม่กรอกข้อมูลทั้ง username และ password)
        if (empty($request->input($this->username())) || empty($request->input('password'))) {
            $request->validate([
                $this->username() => 'required|string',
                'password' => 'required|string',
            ], [
                $this->username().'.required' => 'กรุณากรอกชื่อผู้ใช้',
                $this->username().'.string' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษรหรือตัวเลขเท่านั้น',
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.string' => 'รหัสผ่านต้องเป็นตัวอักษรเท่านั้น',
            ]);
        } else {
            // หากมีการกรอกข้อมูลแล้ว ให้ validate แค่รูปแบบข้อมูล
            $request->validate([
                $this->username() => 'string',
                'password' => 'string',
            ], [
                $this->username().'.string' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษรหรือตัวเลขเท่านั้น',
                'password.string' => 'รหัสผ่านต้องเป็นตัวอักษรเท่านั้น',
            ]);
        }
    }

    public function login(Request $request)
    {
        Log::info('Login attempt', ['request' => $request->all()]);

        try {
            // ตรวจสอบการเชื่อมต่อฐานข้อมูล
            DB::connection()->getPdo();

            // มีการกดปุ่ม Submit แน่นอน แต่อาจไม่มีข้อมูล
            // ตรวจสอบข้อมูลที่จำเป็น
            $errors = [];
            if (empty($request->input($this->username()))) {
                $errors[$this->username()] = 'กรุณากรอกชื่อผู้ใช้';
            }

            if (empty($request->input('password'))) {
                $errors['password'] = 'กรุณากรอกรหัสผ่าน';
            }

            if (!empty($errors)) {
                Log::info('Login validation failed', ['errors' => $errors]);
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors($errors);
            }

            // ถ้าไม่มี errors จากการตรวจสอบเบื้องต้น ทำการ validate รูปแบบข้อมูล
            $this->validateLogin($request);

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if (method_exists($this, 'hasTooManyLoginAttempts') &&
                $this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                return $this->sendLoginResponse($request);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            Log::info('Login credentials incorrect');
            return $this->sendFailedLoginResponse($request);
        } catch (\Exception $e) {
            // ส่ง error ไปที่ 'general' แทน 'email'
            Log::error('Database connection error', ['exception' => $e->getMessage()]);
            return redirect()->back()
                ->withInput($request->only('username', 'remember'))
                ->withErrors(['general' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้ กรุณาตรวจสอบการตั้งค่า']);
        }
    }
}

