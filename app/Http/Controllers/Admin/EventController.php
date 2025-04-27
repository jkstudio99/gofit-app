<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * แสดงรายการกิจกรรมทั้งหมดสำหรับผู้ดูแลระบบ
     */
    public function index(Request $request)
    {
        // กรองตามสถานะ
        $status = $request->input('status', 'all');

        $query = Event::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // เรียงลำดับตามวันที่เริ่มกิจกรรม ล่าสุดขึ้นก่อน
        $events = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.events.index', compact('events', 'status'));
    }

    /**
     * แสดงฟอร์มสร้างกิจกรรมใหม่
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * บันทึกกิจกรรมใหม่
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_desc' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'event_image' => 'nullable|image|max:2048', // ขนาดไม่เกิน 2MB
            'distance' => 'nullable|numeric|min:0',
            'max_participants' => 'required|integer|min:0',
            'status' => 'required|in:published,draft',
        ]);

        // จัดการอัพโหลดรูปภาพ
        if ($request->hasFile('event_image')) {
            $path = $request->file('event_image')->store('events', 'public');
            $validated['event_image'] = $path;
        }

        // สร้างกิจกรรมใหม่
        $event = Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'สร้างกิจกรรมใหม่สำเร็จแล้ว');
    }

    /**
     * แสดงรายละเอียดกิจกรรม
     */
    public function show(Event $event)
    {
        // ดึงข้อมูลผู้เข้าร่วมกิจกรรม
        $participants = EventUser::with('user')
            ->where('event_id', $event->event_id)
            ->orderBy('registered_at')
            ->get();

        // แยกตามสถานะ
        $registeredCount = $participants->where('status', 'registered')->count();
        $attendedCount = $participants->where('status', 'attended')->count();
        $cancelledCount = $participants->where('status', 'cancelled')->count();

        return view('admin.events.show', compact('event', 'participants',
            'registeredCount', 'attendedCount', 'cancelledCount'));
    }

    /**
     * แสดงฟอร์มแก้ไขกิจกรรม
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * อัพเดทข้อมูลกิจกรรม
     */
    public function update(Request $request, Event $event)
    {
        // ตรวจสอบข้อมูล
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_desc' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'event_image' => 'nullable|image|max:2048', // ขนาดไม่เกิน 2MB
            'distance' => 'nullable|numeric|min:0',
            'max_participants' => 'required|integer|min:0',
            'status' => 'required|in:published,draft,cancelled',
        ]);

        // จัดการอัพโหลดรูปภาพใหม่
        if ($request->hasFile('event_image')) {
            // ลบรูปภาพเดิม (ถ้ามี)
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }

            // อัพโหลดรูปภาพใหม่
            $path = $request->file('event_image')->store('events', 'public');
            $validated['event_image'] = $path;
        }

        // อัพเดทกิจกรรม
        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'อัพเดทกิจกรรมสำเร็จแล้ว');
    }

    /**
     * ลบกิจกรรม
     */
    public function destroy(Event $event)
    {
        // ลบรูปภาพ (ถ้ามี)
        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        // ลบกิจกรรม (ข้อมูลลงทะเบียนจะถูกลบอัตโนมัติด้วย CASCADE)
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'ลบกิจกรรมสำเร็จแล้ว');
    }

    /**
     * อัพเดทสถานะผู้เข้าร่วมกิจกรรม
     */
    public function updateParticipantStatus(Request $request, Event $event, $userId)
    {
        $validated = $request->validate([
            'status' => 'required|in:registered,attended,cancelled',
        ]);

        // ค้นหารายการลงทะเบียน
        $registration = EventUser::where('event_id', $event->event_id)
            ->where('user_id', $userId)
            ->firstOrFail();

        // อัพเดทสถานะ
        $registration->status = $validated['status'];
        $registration->save();

        return redirect()->route('admin.events.show', $event)
            ->with('success', 'อัพเดทสถานะผู้เข้าร่วมสำเร็จแล้ว');
    }

    /**
     * ดาวน์โหลดรายชื่อผู้เข้าร่วมกิจกรรมเป็น CSV
     */
    public function exportParticipants(Event $event)
    {
        $participants = EventUser::with('user')
            ->where('event_id', $event->event_id)
            ->get();

        $filename = 'participants_' . $event->event_id . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($participants) {
            $file = fopen('php://output', 'w');

            // หัวตาราง CSV
            fputcsv($file, ['ID', 'ชื่อผู้ใช้', 'อีเมล', 'เบอร์โทร', 'สถานะ', 'วันที่ลงทะเบียน']);

            // ข้อมูลแถว
            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->user->user_id,
                    $participant->user->name,
                    $participant->user->email,
                    $participant->user->phone ?? 'ไม่มีข้อมูล',
                    $participant->getStatusInThai(),
                    $participant->registered_at->format('d/m/Y H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
