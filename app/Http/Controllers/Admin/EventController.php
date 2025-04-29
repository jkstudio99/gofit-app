<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * แสดงรายการกิจกรรมทั้งหมดสำหรับผู้ดูแลระบบ
     */
    public function index(Request $request)
    {
        // กรองตามสถานะ
        $status = $request->input('status', 'all');
        $search = $request->input('search', '');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $location = $request->input('location', '');

        // Add debugging logs
        Log::info('Filter parameters:', [
            'status' => $status,
            'search' => $search,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'location' => $location,
        ]);

        $query = Event::query();

        // ค้นหาตามชื่อหรือรายละเอียด
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // กรองตามสถานะ
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // กรองตามวันที่เริ่ม
        if (!empty($startDate)) {
            Log::info('Applying start date filter', ['startDate' => $startDate]);
            try {
                $formattedStartDate = Carbon::parse($startDate)->startOfDay();
                $query->where('start_datetime', '>=', $formattedStartDate);
            } catch (\Exception $e) {
                Log::error('Error parsing start date', ['error' => $e->getMessage()]);
            }
        }

        // กรองตามวันที่สิ้นสุด
        if (!empty($endDate)) {
            Log::info('Applying end date filter', ['endDate' => $endDate]);
            try {
                $formattedEndDate = Carbon::parse($endDate)->endOfDay();
                $query->where('end_datetime', '<=', $formattedEndDate);
            } catch (\Exception $e) {
                Log::error('Error parsing end date', ['error' => $e->getMessage()]);
            }
        }

        // กรองตามสถานที่
        if (!empty($location)) {
            Log::info('Applying location filter', ['location' => $location]);
            $query->where('location', 'like', "%{$location}%");
        }

        // เรียงลำดับตามวันที่สร้าง ล่าสุดขึ้นก่อน
        $events = $query->orderBy('created_at', 'desc')->paginate(10);

        // Log the final query and result count
        Log::info('Event query results', [
            'count' => $events->count(),
            'total' => $events->total()
        ]);

        // ส่งข้อมูลไปยัง view
        return view('admin.events.index', compact('events', 'status', 'search', 'startDate', 'endDate', 'location'));
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

        // แมป event_name ไปยัง title เพื่อให้ตรงกับ database
        $validated['title'] = $validated['event_name'];

        // เพิ่ม created_by เป็น user ID ของผู้ใช้ที่กำลัง login
        $validated['created_by'] = auth()->id();

        // จัดการอัพโหลดรูปภาพ
        if ($request->hasFile('event_image')) {
            $path = $request->file('event_image')->store('events', 'public');
            $validated['event_image'] = $path;
            $validated['image_url'] = $path; // Also set image_url to ensure compatibility
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

        // แมป event_name ไปยัง title เพื่อให้ตรงกับ database
        $validated['title'] = $validated['event_name'];

        // จัดการอัพโหลดรูปภาพใหม่
        if ($request->hasFile('event_image')) {
            // ลบรูปภาพเดิม (ถ้ามี)
            if ($event->event_image) {
                Storage::disk('public')->delete($event->event_image);
            }

            // อัพโหลดรูปภาพใหม่
            $path = $request->file('event_image')->store('events', 'public');
            $validated['event_image'] = $path;
            $validated['image_url'] = $path; // Also set image_url to ensure compatibility
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
        // ดึงข้อมูลผู้ลงทะเบียนก่อนที่จะลบกิจกรรม
        $participants = EventUser::with('user')
            ->where('event_id', $event->event_id)
            ->whereIn('status', ['registered', 'attended'])
            ->get();

        // ส่งการแจ้งเตือนให้ผู้ลงทะเบียน
        foreach ($participants as $participant) {
            // ส่งการแจ้งเตือนให้ผู้ใช้ (ถ้ามี Notification system)
            try {
                $user = $participant->user;

                // ถ้ามี Notification Model ให้ใช้แบบนี้
                // Notification::create([
                //     'user_id' => $user->user_id,
                //     'title' => 'กิจกรรมถูกยกเลิก',
                //     'message' => 'กิจกรรม "' . $event->event_name . '" ที่คุณลงทะเบียนได้ถูกยกเลิกแล้ว',
                //     'type' => 'event_cancelled',
                //     'read' => false,
                // ]);

                // ถ้ามี Laravel Notification ให้ใช้แบบนี้
                // $user->notify(new EventCancelled($event));

                // สำหรับตอนนี้ให้ล็อกการยกเลิกไว้
                Log::info('Sending cancellation notification to user: ' . $user->username . ' for event: ' . $event->event_name);
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation notification: ' . $e->getMessage());
            }
        }

        // ลบรูปภาพ (ถ้ามี)
        if ($event->event_image) {
            Storage::disk('public')->delete($event->event_image);
        }

        // ลบกิจกรรม (ข้อมูลลงทะเบียนจะถูกลบอัตโนมัติด้วย CASCADE)
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'ลบกิจกรรมสำเร็จแล้ว การแจ้งเตือนถูกส่งไปยังผู้ลงทะเบียนทั้งหมด');
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

    /**
     * API สำหรับค้นหาอัตโนมัติ
     */
    public function searchAutocomplete(Request $request)
    {
        // Log the request for debugging
        Log::info('Autocomplete search request received', [
            'term' => $request->input('term'),
            'all_params' => $request->all()
        ]);

        $term = $request->input('term', '');

        if (empty($term)) {
            return response()->json([]);
        }

        $events = Event::where('title', 'like', "%{$term}%")
                  ->select('title as value', 'event_id')
                  ->limit(10)
                  ->get();

        // Log the response for debugging
        Log::info('Autocomplete search response', [
            'count' => $events->count(),
            'results' => $events->toArray()
        ]);

        return response()->json($events);
    }
}
