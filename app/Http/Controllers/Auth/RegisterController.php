<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'firstname.required' => 'กรุณากรอกชื่อของคุณ',
            'firstname.string' => 'ชื่อต้องเป็นตัวอักษรเท่านั้น',
            'firstname.max' => 'ชื่อต้องไม่เกิน 255 ตัวอักษร',

            'lastname.required' => 'กรุณากรอกนามสกุลของคุณ',
            'lastname.string' => 'นามสกุลต้องเป็นตัวอักษรเท่านั้น',
            'lastname.max' => 'นามสกุลต้องไม่เกิน 255 ตัวอักษร',

            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.string' => 'ชื่อผู้ใช้ต้องเป็นตัวอักษรหรือตัวเลขเท่านั้น',
            'username.max' => 'ชื่อผู้ใช้ต้องไม่เกิน 50 ตัวอักษร',
            'username.unique' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'username.alpha_dash' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษร ตัวเลข ขีดกลาง หรือขีดล่างเท่านั้น',

            'email.required' => 'กรุณากรอกอีเมล',
            'email.string' => 'อีเมลต้องเป็นตัวอักษรเท่านั้น',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.max' => 'อีเมลต้องไม่เกิน 255 ตัวอักษร',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',

            'telephone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'telephone.string' => 'เบอร์โทรศัพท์ต้องเป็นตัวเลขเท่านั้น',
            'telephone.regex' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง',

            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.string' => 'รหัสผ่านต้องเป็นตัวอักษรเท่านั้น',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'ยืนยันรหัสผ่านไม่ตรงกัน',

            'accept_terms.required' => 'กรุณายอมรับเงื่อนไขการใช้งานและนโยบายความเป็นส่วนตัว',
        ];

        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'accept_terms' => ['required'],
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'telephone' => $data['telephone'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
