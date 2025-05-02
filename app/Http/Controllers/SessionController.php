<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    /**
     * Display a listing of active sessions for admin
     */
    public function index()
    {
        // ตรวจสอบสิทธิ์ admin
        if (!Auth::user() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $sessions = Session::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * สร้าง session ใหม่เมื่อผู้ใช้ล็อกอิน
     *
     * @param User $user ผู้ใช้ที่ล็อกอิน
     * @param Request $request ข้อมูล request
     * @return Session
     */
    public function createUserSession(User $user, Request $request)
    {
        // สร้าง token แบบสุ่ม
        $token = Str::random(64);

        // ตั้งค่าเวลาหมดอายุ (30 วัน)
        $expiredAt = Carbon::now()->addDays(30);

        // สร้าง session ใหม่
        $session = Session::create([
            'user_id' => $user->user_id,
            'session_token' => $token,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'expired_at' => $expiredAt
        ]);

        // บันทึก session token ใน cookie
        cookie('session_token', $token, 43200); // 30 วัน

        return $session;
    }

    /**
     * ตรวจสอบว่า session ยังใช้งานได้หรือไม่
     *
     * @param string $token Session token
     * @return bool|Session
     */
    public function validateSession($token)
    {
        $session = Session::where('session_token', $token)
            ->where('expired_at', '>', Carbon::now())
            ->first();

        if (!$session) {
            return false;
        }

        return $session;
    }

    /**
     * ต่ออายุ session
     *
     * @param string $token Session token
     * @return bool
     */
    public function renewSession($token)
    {
        $session = $this->validateSession($token);

        if (!$session) {
            return false;
        }

        // ต่ออายุ session อีก 30 วัน
        $session->expired_at = Carbon::now()->addDays(30);
        $session->updated_at = Carbon::now();
        $session->save();

        // ต่ออายุ cookie
        cookie('session_token', $token, 43200); // 30 วัน

        return true;
    }

    /**
     * ลบ session เมื่อผู้ใช้ล็อกเอาต์
     *
     * @param string $token Session token
     * @return bool
     */
    public function destroySession($token = null)
    {
        if (!$token) {
            $token = request()->cookie('session_token');
        }

        if (!$token) {
            return false;
        }

        // ลบ session จากฐานข้อมูล
        Session::where('session_token', $token)->delete();

        // ลบ cookie
        cookie()->forget('session_token');

        return true;
    }

    /**
     * ลบ sessions ทั้งหมดของผู้ใช้ (ล็อกเอาต์จากทุกอุปกรณ์)
     *
     * @param int $userId ID ของผู้ใช้
     * @return bool
     */
    public function destroyAllUserSessions($userId)
    {
        Session::where('user_id', $userId)->delete();

        // ลบ cookie ปัจจุบัน
        cookie()->forget('session_token');

        return true;
    }

    /**
     * แสดงรายการ sessions ของผู้ใช้ปัจจุบัน
     */
    public function userSessions()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $sessions = Session::where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.sessions', compact('sessions'));
    }

    /**
     * ลบ session เฉพาะรายการ (สำหรับผู้ใช้ล็อกเอาต์จากอุปกรณ์อื่น)
     */
    public function destroySpecificSession(Request $request, $sessionId)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $session = Session::where('session_id', $sessionId)
            ->where('user_id', $user->user_id)
            ->first();

        if (!$session) {
            return back()->with('error', 'ไม่พบ Session ที่ระบุ');
        }

        // ตรวจสอบว่าไม่ใช่ session ปัจจุบัน
        if ($session->session_token === $request->cookie('session_token')) {
            return back()->with('error', 'ไม่สามารถลบ Session ปัจจุบันได้');
        }

        $session->delete();

        return back()->with('success', 'ลบ Session สำเร็จ');
    }

    /**
     * ล้าง sessions ที่หมดอายุจากระบบ (ควรเรียกผ่าน cron job)
     */
    public function clearExpiredSessions()
    {
        $deleted = Session::where('expired_at', '<', Carbon::now())->delete();

        return response()->json([
            'success' => true,
            'deleted_count' => $deleted
        ]);
    }
}
