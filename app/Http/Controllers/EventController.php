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
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * แสดงรายการกิจกรรมทั้งหมด
     */
    public function index(Request $request)
    {
        // เริ่มต้นด้วยการดึงข้อมูลทั้งหมด แล้วค่อยกรองตามเงื่อนไข
        $query = Event::query();

        // เพิ่มเงื่อนไขสถานะ published สำหรับผู้ใช้ทั่วไป (ไม่ใช่แอดมิน)
        if (Auth::check() && Auth::user()->role_id == 1) {
            // แอดมินเห็นทุกสถานะ
            Log::info('User is admin, showing all events');
        } else {
            // ผู้ใช้ทั่วไปเห็นเฉพาะที่ published
            $query->where('status', 'published');
            Log::info('User is not admin or not logged in, showing only published events');
        }

        // ค้นหาตามคำค้น
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%");
            });
        }

        // กรองตามประเภท
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // กรองตามสถานะ
        if ($request->has('status')) {
            if ($request->status == 'upcoming') {
                $query->where('start_datetime', '>', now());
            } elseif ($request->status == 'active') {
                $query->where('start_datetime', '<=', now())
                      ->where('end_datetime', '>=', now());
            } elseif ($request->status == 'past') {
                $query->where('end_datetime', '<', now());
            }
            // 'all' ไม่ต้องมีเงื่อนไขพิเศษ
        }

        // จัดเรียง
        if ($request->has('sort')) {
            if ($request->sort == 'date_asc') {
                $query->orderBy('start_datetime', 'asc');
            } elseif ($request->sort == 'date_desc') {
                $query->orderBy('start_datetime', 'desc');
            } elseif ($request->sort == 'popularity') {
                $query->withCount('participants')
                      ->orderBy('participants_count', 'desc');
            }
        } else {
            $query->orderBy('start_datetime', 'asc');
        }

        // ดึงข้อมูลเพิ่มเติม
        $query->withCount(['participants' => function($query) {
            $query->where('status', 'registered');
        }]);

        // บันทึก log เพื่อตรวจสอบจำนวนกิจกรรมทั้งหมดก่อนแบ่งหน้า
        $totalCount = $query->count();
        Log::info('Event count before pagination: ' . $totalCount);

        // แบ่งหน้า - เพิ่มจำนวนรายการต่อหน้าเป็น 20
        $events = $query->paginate(20);

        // ดึงหมวดหมู่ของกิจกรรม
        $categories = Event::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('events.index', compact('events', 'categories', 'totalCount'));
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

        // ดึงกิจกรรมที่เกี่ยวข้อง (มีประเภทเดียวกัน แต่ไม่ใช่กิจกรรมปัจจุบัน)
        $relatedEvents = Event::where('status', 'published')
            ->where('event_id', '!=', $id)
            ->when($event->category, function($query) use ($event) {
                return $query->where('category', $event->category);
            })
            ->latest('created_at')
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'userRegistration', 'relatedEvents'));
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
    public function cancel($id)
    {
        $event = Event::findOrFail($id);

        // ตรวจสอบว่ามีการลงทะเบียนแล้วหรือไม่
        $registration = EventUser::where('event_id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'registered')
            ->first();

        if (!$registration) {
            return redirect()->route('events.show', $id)
                ->with('error', 'คุณไม่ได้ลงทะเบียนเข้าร่วมกิจกรรมนี้');
        }

        // ตรวจสอบว่าสามารถยกเลิกได้หรือไม่ (ยกเลิกได้เฉพาะก่อนเริ่มกิจกรรม)
        if ($event->hasStarted()) {
        return redirect()->route('events.show', $id)
                ->with('error', 'ไม่สามารถยกเลิกการลงทะเบียนได้เนื่องจากกิจกรรมเริ่มแล้ว');
        }

        // อัปเดตสถานะเป็น "ยกเลิก"
        $registration->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'ยกเลิกการลงทะเบียนเรียบร้อยแล้ว');
    }

    /**
     * ยกเลิกการลงทะเบียน (เพื่อความเข้ากันได้กับโค้ดเดิม)
     */
    public function cancelRegistration($id)
    {
        return $this->cancel($id);
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
     * แสดงกิจกรรมของผู้ใช้ที่ลงทะเบียนหรือเป็นผู้จัด
     */
    public function myEvents(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status', 'all');

        // สร้าง query สำหรับการลงทะเบียนของผู้ใช้
        $query = EventUser::where('user_id', $user->user_id)
            ->with(['event' => function($query) {
                $query->withCount('participants');
            }]);

        // กรองตามสถานะการลงทะเบียน
        if ($status === 'cancelled') {
            $query->where('status', 'cancelled');
        } elseif ($status !== 'all') {
            $query->where('status', 'registered');

            // กรองตามสถานะกิจกรรม
            if ($status === 'upcoming') {
                $query->whereHas('event', function($q) {
                    $q->where('start_datetime', '>', now());
                });
            } elseif ($status === 'active') {
                $query->whereHas('event', function($q) {
                    $q->where('start_datetime', '<=', now())
                      ->where('end_datetime', '>=', now());
                });
            } elseif ($status === 'past') {
                $query->whereHas('event', function($q) {
                    $q->where('end_datetime', '<', now());
                });
            }
        }

        // เรียงลำดับตามวันที่เริ่มกิจกรรม (ใช้ left join แทนเพื่อให้ได้ข้อมูลทั้งหมด)
        $query->select('tb_event_users.*')
              ->leftJoin('tb_events', 'tb_event_users.event_id', '=', 'tb_events.event_id')
              ->orderBy('tb_events.start_datetime', 'asc');

        // ดึงข้อมูล
        $registrations = $query->paginate(9);

        return view('events.my-events', compact('registrations', 'status'));
    }
}
