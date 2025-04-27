<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * แสดงรายการกิจกรรมทั้งหมด
     */
    public function index()
    {
        $events = Event::where('status', 'published')
            ->where('end_datetime', '>=', now())
            ->orderBy('start_datetime')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * แสดงรายละเอียดกิจกรรม
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        // ตรวจสอบว่าผู้ใช้ปัจจุบันลงทะเบียนแล้วหรือไม่
        $userRegistration = null;

        if (Auth::check()) {
            $userRegistration = EventUser::where('event_id', $id)
                ->where('user_id', Auth::id())
                ->first();
        }

        return view('events.show', compact('event', 'userRegistration'));
    }

    /**
     * แสดงฟอร์มสร้างกิจกรรมใหม่
     */
    public function create()
    {
        // ตรวจสอบสิทธิ์
        $this->authorize('create', Event::class);

        return view('events.create');
    }

    /**
     * บันทึกกิจกรรมใหม่
     */
    public function store(Request $request)
    {
        // ตรวจสอบสิทธิ์
        $this->authorize('create', Event::class);

        // ตรวจสอบข้อมูล
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'capacity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        // อัปโหลดรูปภาพ (ถ้ามี)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        // เพิ่มข้อมูลผู้สร้าง
        $validated['created_by'] = Auth::id();

        // สร้างกิจกรรมใหม่
        $event = Event::create($validated);

        return redirect()->route('events.show', $event->event_id)
            ->with('success', 'สร้างกิจกรรมเรียบร้อยแล้ว');
    }

    /**
     * แสดงฟอร์มแก้ไขกิจกรรม
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);

        // ตรวจสอบสิทธิ์
        $this->authorize('update', $event);

        return view('events.edit', compact('event'));
    }

    /**
     * อัปเดตข้อมูลกิจกรรม
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // ตรวจสอบสิทธิ์
        $this->authorize('update', $event);

        // ตรวจสอบข้อมูล
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'capacity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        // อัปโหลดรูปภาพใหม่ (ถ้ามี)
        if ($request->hasFile('image')) {
            // ลบรูปเก่า (ถ้ามี)
            if ($event->image_url) {
                $oldPath = Str::replaceFirst('storage', 'public', $event->image_url);
                Storage::delete($oldPath);
            }

            $path = $request->file('image')->store('events', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        // อัปเดตข้อมูล
        $event->update($validated);

        return redirect()->route('events.show', $event->event_id)
            ->with('success', 'อัปเดตกิจกรรมเรียบร้อยแล้ว');
    }

    /**
     * ลงทะเบียนเข้าร่วมกิจกรรม
     */
    public function register($id)
    {
        $event = Event::findOrFail($id);

        // ตรวจสอบว่าสามารถลงทะเบียนได้หรือไม่
        if (!$event->canRegister()) {
            return redirect()->route('events.show', $id)
                ->with('error', 'ไม่สามารถลงทะเบียนได้ กิจกรรมอาจเต็ม หรือสิ้นสุดแล้ว');
        }

        // ตรวจสอบว่าเคยลงทะเบียนแล้วหรือไม่
        $existingRegistration = EventUser::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingRegistration) {
            // ถ้าเคยยกเลิกแล้ว ให้เปลี่ยนสถานะกลับเป็นลงทะเบียน
            if ($existingRegistration->status === 'cancelled') {
                $existingRegistration->update([
                    'status' => 'registered',
                    'registered_at' => now()
                ]);

                return redirect()->route('events.show', $id)
                    ->with('success', 'ลงทะเบียนเข้าร่วมกิจกรรมเรียบร้อยแล้ว');
            }

            return redirect()->route('events.show', $id)
                ->with('info', 'คุณได้ลงทะเบียนเข้าร่วมกิจกรรมนี้แล้ว');
        }

        // สร้างการลงทะเบียนใหม่
        EventUser::create([
            'event_id' => $id,
            'user_id' => Auth::id(),
            'status' => 'registered',
            'registered_at' => now()
        ]);

        return redirect()->route('events.show', $id)
            ->with('success', 'ลงทะเบียนเข้าร่วมกิจกรรมเรียบร้อยแล้ว');
    }

    /**
     * ยกเลิกการลงทะเบียน
     */
    public function cancelRegistration($id)
    {
        $registration = EventUser::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // เปลี่ยนสถานะเป็นยกเลิก
        $registration->update(['status' => 'cancelled']);

        return redirect()->route('events.show', $id)
            ->with('success', 'ยกเลิกการลงทะเบียนเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายชื่อผู้ลงทะเบียน (สำหรับผู้ดูแล)
     */
    public function participants($id)
    {
        $event = Event::with('registrations.user')->findOrFail($id);

        // ตรวจสอบสิทธิ์
        $this->authorize('viewParticipants', $event);

        return view('events.participants', compact('event'));
    }

    /**
     * อัปเดตสถานะการเข้าร่วม (สำหรับผู้ดูแล)
     */
    public function updateAttendance(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // ตรวจสอบสิทธิ์
        $this->authorize('viewParticipants', $event);

        // ตรวจสอบข้อมูล
        $validated = $request->validate([
            'registrations' => 'required|array',
            'registrations.*.id' => 'required|exists:event_users,id',
            'registrations.*.status' => 'required|in:registered,attended,absent',
        ]);

        // อัปเดตสถานะการเข้าร่วม
        foreach ($validated['registrations'] as $registration) {
            EventUser::where('id', $registration['id'])->update([
                'status' => $registration['status']
            ]);
        }

        return redirect()->route('events.participants', $id)
            ->with('success', 'อัปเดตสถานะการเข้าร่วมเรียบร้อยแล้ว');
    }

    /**
     * แสดงกิจกรรมของผู้ใช้ปัจจุบัน
     */
    public function myEvents()
    {
        $registeredEvents = Auth::user()->eventRegistrations()
            ->where('status', 'registered')
            ->with('event')
            ->get()
            ->pluck('event');

        return view('events.my-events', compact('registeredEvents'));
    }
}
