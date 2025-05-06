<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Run;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RunStatisticsController extends Controller
{
    /**
     * Display running statistics page.
     */
    public function index()
    {
        // Get total runs
        $totalRuns = Run::count();

        // Get total distance
        $totalDistance = Run::sum('distance');

        // Get total calories burned
        $totalCalories = Run::sum('calories_burned');

        // Get total duration
        $totalDuration = Run::sum('duration');
        $formattedTotalDuration = $this->formatDuration($totalDuration);

        // Get top runners
        $topRunners = User::select(
                'tb_user.user_id',
                'tb_user.username',
                'tb_user.firstname',
                'tb_user.lastname',
                DB::raw('COUNT(tb_run.run_id) as run_count'),
                DB::raw('SUM(tb_run.distance) as total_distance'),
                DB::raw('SUM(tb_run.calories_burned) as total_calories')
            )
            ->join('tb_run', 'tb_user.user_id', '=', 'tb_run.user_id')
            ->groupBy('tb_user.user_id', 'tb_user.username', 'tb_user.firstname', 'tb_user.lastname')
            ->orderBy('total_distance', 'desc')
            ->take(10)
            ->get();

        // Get latest runs
        $latestRuns = Run::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Get last week stats
        $lastWeekStats = $this->getLastWeekStats();

        // Get all users for export form
        $users = User::select('user_id', 'username')->orderBy('username')->get();

        return view('admin.run.stats', compact(
            'totalRuns',
            'totalDistance',
            'totalCalories',
            'formattedTotalDuration',
            'topRunners',
            'latestRuns',
            'lastWeekStats',
            'users'
        ));
    }

    /**
     * Display user-specific running statistics.
     */
    public function userStats($userId)
    {
        $user = User::findOrFail($userId);

        // Get user's runs
        $userRuns = Run::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get user's statistics
        $userStats = [
            'total_runs' => Run::where('user_id', $userId)->count(),
            'total_distance' => Run::where('user_id', $userId)->sum('distance'),
            'total_calories' => Run::where('user_id', $userId)->sum('calories_burned'),
            'total_duration' => $this->formatDuration(Run::where('user_id', $userId)->sum('duration')),
            'average_speed' => Run::where('user_id', $userId)->avg('average_speed') ?? 0,
        ];

        // Get monthly statistics
        $monthlyStats = Run::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(distance) as distance'),
                DB::raw('SUM(calories_burned) as calories')
            )
            ->where('user_id', $userId)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('admin.run.user-stats', compact('user', 'userRuns', 'userStats', 'monthlyStats'));
    }

    /**
     * Get statistics for the last week.
     */
    private function getLastWeekStats()
    {
        $stats = [];

        // Get the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Run::whereDate('created_at', $date)->count();

            $stats[] = [
                'date' => $date->format('d M'),
                'count' => $count
            ];
        }

        return $stats;
    }

    /**
     * Format duration in seconds to hours and minutes.
     */
    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return $hours . ' ชม. ' . $minutes . ' นาที';
    }

    /**
     * Display running activities in calendar view.
     */
    public function calendar(Request $request)
    {
        // Get month and year from request or use current month/year
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Get all runs for the selected month
        $runs = Run::with('user')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            });

        // Get the first day of the month
        $firstDayOfMonth = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $firstDayOfMonth->daysInMonth;

        // Monthly statistics
        $monthlyStats = [
            'total_runs' => Run::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count(),
            'total_distance' => Run::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('distance'),
            'total_calories' => Run::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('calories_burned'),
            'active_users' => Run::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->distinct('user_id')
                ->count('user_id')
        ];

        // Get previous and next month
        $prevMonth = Carbon::createFromDate($year, $month, 1)->subMonth();
        $nextMonth = Carbon::createFromDate($year, $month, 1)->addMonth();

        return view('admin.run.calendar', compact(
            'runs',
            'firstDayOfMonth',
            'daysInMonth',
            'monthlyStats',
            'prevMonth',
            'nextMonth',
            'month',
            'year'
        ));
    }

    /**
     * แสดงแผนที่ความร้อนของกิจกรรมการวิ่ง
     */
    public function heatmap()
    {
        // ดึงข้อมูลผู้ใช้ทั้งหมดที่มีกิจกรรมการวิ่ง
        $users = User::whereHas('runs', function($query) {
            $query->where('is_completed', true);
        })->get();

        return view('admin.run.heatmap', compact('users'));
    }

    /**
     * ดึงข้อมูลสำหรับแผนที่ความร้อน
     */
    public function heatmapData(Request $request)
    {
        // กำหนดช่วงเวลา
        $days = $request->input('days', 30);
        $startDate = null;
        $endDate = null;

        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subDays($days);
        }

        // กรองตามผู้ใช้ (ถ้ามี)
        $userId = $request->input('user_id', 'all');

        // สร้างคิวรี่พื้นฐาน
        $runsQuery = Run::where('is_completed', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($userId !== 'all') {
            $runsQuery->where('user_id', $userId);
        }

        // ดึงข้อมูลการวิ่งทั้งหมดในช่วงเวลาที่กำหนด
        $runs = $runsQuery->get();

        // สร้างข้อมูลสำหรับแผนที่ความร้อน
        $heatmapData = [];
        $areasCount = []; // สำหรับนับจำนวนครั้งในแต่ละพื้นที่

        foreach ($runs as $run) {
            // ข้ามรายการที่ไม่มีข้อมูลเส้นทาง
            if (!$run->route_data || !is_array($run->route_data)) {
                continue;
            }

            // วนลูปเพื่อดึงข้อมูลพิกัดจากเส้นทาง
            foreach ($run->route_data as $point) {
                if (isset($point['lat']) && isset($point['lng'])) {
                    // ปัดเศษทศนิยมเพื่อจัดกลุ่มพื้นที่
                    $roundedLat = round($point['lat'], 3);
                    $roundedLng = round($point['lng'], 3);
                    $key = "{$roundedLat},{$roundedLng}";

                    // นับจำนวนครั้งในแต่ละพื้นที่
                    if (!isset($areasCount[$key])) {
                        $areasCount[$key] = [
                            'lat' => $point['lat'],
                            'lng' => $point['lng'],
                            'count' => 1,
                            'total_distance' => $run->distance,
                            'runs' => 1
                        ];
                    } else {
                        $areasCount[$key]['count']++;
                        $areasCount[$key]['total_distance'] += $run->distance;

                        // ตรวจสอบว่าเป็นการวิ่งรายการเดียวกันหรือไม่
                        $isNewRun = true;
                        if (isset($areasCount[$key]['last_run_id']) && $areasCount[$key]['last_run_id'] === $run->run_id) {
                            $isNewRun = false;
                        }

                        if ($isNewRun) {
                            $areasCount[$key]['runs']++;
                            $areasCount[$key]['last_run_id'] = $run->run_id;
                        }
                    }

                    // เพิ่มข้อมูลสำหรับแผนที่ความร้อน
                    $heatmapData[] = [
                        'lat' => $point['lat'],
                        'lng' => $point['lng'],
                        'count' => 1
                    ];
                }
            }
        }

        // หาพื้นที่ยอดนิยม (เรียงตามจำนวนการวิ่ง)
        $popularAreas = collect($areasCount)->sortByDesc('runs')->values()->take(6);
        $popularAreas = $popularAreas->map(function ($area) {
            return [
                'lat' => $area['lat'],
                'lng' => $area['lng'],
                'name' => $this->getAreaName($area['lat'], $area['lng']),
                'count' => $area['runs'],
                'avg_distance' => $area['total_distance'] / $area['runs']
            ];
        });

        return response()->json([
            'heatmap_data' => $heatmapData,
            'popular_areas' => $popularAreas
        ]);
    }

    /**
     * ดึงชื่อสถานที่จากพิกัด (Reverse Geocoding)
     *
     * หมายเหตุ: ในการใช้งานจริง ควรใช้บริการ Geocoding API เช่น Google Maps, OpenStreetMap
     * แต่เพื่อความเรียบง่าย เราจะใช้ข้อมูลสมมติในตัวอย่างนี้
     */
    private function getAreaName($lat, $lng)
    {
        // สำหรับตัวอย่าง เราจะใช้ชื่อสมมติตามพิกัด
        // ในการใช้งานจริง ควรใช้บริการ Reverse Geocoding
        $areas = [
            ['lat' => 13.736717, 'lng' => 100.523186, 'name' => 'กรุงเทพมหานคร'],
            ['lat' => 13.756331, 'lng' => 100.501765, 'name' => 'สวนลุมพินี'],
            ['lat' => 13.7563, 'lng' => 100.5016, 'name' => 'สวนลุมพินี'],
            ['lat' => 13.7278, 'lng' => 100.5241, 'name' => 'สวนเบญจกิติ'],
            ['lat' => 13.7469, 'lng' => 100.5349, 'name' => 'ราชประสงค์'],
            ['lat' => 13.7308, 'lng' => 100.5340, 'name' => 'สยาม'],
            ['lat' => 13.7529, 'lng' => 100.4935, 'name' => 'จตุจักร'],
        ];

        // หาสถานที่ที่ใกล้เคียงที่สุด
        $closestArea = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($areas as $area) {
            $distance = $this->calculateDistance($lat, $lng, $area['lat'], $area['lng']);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestArea = $area;
            }
        }

        // ถ้าระยะห่างน้อยกว่า 3 กิโลเมตร ให้ใช้ชื่อสถานที่นั้น
        if ($minDistance < 3) {
            return $closestArea['name'];
        }

        // ถ้าไม่พบสถานที่ที่ใกล้เคียง ให้ใช้ชื่อตามพิกัด
        return "พื้นที่ {$lat}, {$lng}";
    }

    /**
     * คำนวณระยะทางระหว่างสองพิกัด (กิโลเมตร)
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // รัศมีของโลกในกิโลเมตร

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * ส่งออกข้อมูลการวิ่ง
     */
    public function exportData(Request $request)
    {
        // ตรวจสอบความถูกต้องของข้อมูลนำเข้า
        $request->validate([
            'format' => 'required|in:csv,excel',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'user_id' => 'nullable'
        ]);

        // กำหนดช่วงเวลา
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from'))->startOfDay() : Carbon::now()->subMonths(3)->startOfDay();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to'))->endOfDay() : Carbon::now()->endOfDay();

        // สร้างคิวรี่พื้นฐาน - แก้ไขให้ดึงข้อมูลเสมอ
        $query = Run::with('user')->where('is_completed', true);

        // กรองตามวันที่
        $query->whereBetween('created_at', [$dateFrom, $dateTo]);

        // กรองตามผู้ใช้ (ถ้ามี)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // ดึงข้อมูล
        $runs = $query->get();

        // เตรียมข้อมูลสำหรับส่งออก
        $exportData = [];

        foreach ($runs as $run) {
            // แปลงวันที่เป็นรูปแบบไทย
            $createdAt = $run->created_at;
            $thaiCreatedAt = $this->formatThaiDate($createdAt);

            $startTime = $run->start_time ? (is_object($run->start_time) ? $run->start_time : Carbon::parse($run->start_time)) : null;
            $thaiStartTime = $startTime ? $this->formatThaiDate($startTime) : '';

            $endTime = $run->end_time ? (is_object($run->end_time) ? $run->end_time : Carbon::parse($run->end_time)) : null;
            $thaiEndTime = $endTime ? $this->formatThaiDate($endTime) : '';

            $exportRow = [
                'run_id' => $run->run_id,
                'user_id' => $run->user_id,
                'username' => $run->user->username ?? 'Unknown',
                'distance' => $run->distance,
                'duration' => $run->duration,
                'duration_formatted' => $this->formatDurationTime($run->duration),
                'average_speed' => $run->average_speed,
                'calories_burned' => $run->calories_burned,
                'start_time' => $thaiStartTime,
                'end_time' => $thaiEndTime,
                'created_at' => $thaiCreatedAt
            ];

            $exportData[] = $exportRow;
        }

        // ส่งออกตามรูปแบบที่กำหนด
        $format = $request->input('format', 'excel');

        switch ($format) {
            case 'csv':
                return $this->exportCsv($exportData);
            case 'excel':
                return $this->exportExcel($exportData);
            default:
                return $this->exportExcel($exportData); // Default to Excel
        }
    }

    /**
     * แปลงวันที่เป็นรูปแบบไทย
     */
    private function formatThaiDate($date)
    {
        if (!$date) return '';

        $thaiMonths = [
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
        ];

        $day = $date->format('j');
        $month = $thaiMonths[$date->format('n')];
        $year = $date->format('Y') + 543; // แปลงเป็น พ.ศ.
        $time = $date->format('H:i');

        return "{$day} {$month} {$year} {$time}";
    }

    /**
     * แปลงระยะเวลาเป็นรูปแบบ HH:MM:SS
     */
    private function formatDurationTime($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    /**
     * ส่งออกข้อมูลในรูปแบบ CSV
     */
    private function exportCsv($data)
    {
        // สร้างข้อมูลตัวอย่างถ้าไม่มีข้อมูล
        if (empty($data)) {
            $currentDate = Carbon::now();
            $thaiDate = $this->formatThaiDate($currentDate);

            $data = [
                [
                    'run_id' => '',
                    'user_id' => '',
                    'username' => '',
                    'distance' => 0,
                    'duration' => 0,
                    'duration_formatted' => '00:00:00',
                    'average_speed' => 0,
                    'calories_burned' => 0,
                    'start_time' => '',
                    'end_time' => '',
                    'created_at' => $thaiDate
                ]
            ];
        }

        // สร้างชื่อไฟล์
        $filename = 'running_data_' . date('Y-m-d_His') . '.csv';

        // สร้างเซสชันที่กำหนดการตอบกลับ
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // สร้าง callback function เพื่อสร้างเนื้อหา CSV
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            // Set UTF-8 BOM for Excel to display Thai characters correctly
            fputs($file, "\xEF\xBB\xBF");

            // เขียนหัวตาราง
            fputcsv($file, array_keys($data[0]));

            // เขียนข้อมูล
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * ส่งออกข้อมูลในรูปแบบ Excel
     */
    private function exportExcel($data)
    {
        // สร้างข้อมูลตัวอย่างถ้าไม่มีข้อมูล
        if (empty($data)) {
            $currentDate = Carbon::now();
            $thaiDate = $this->formatThaiDate($currentDate);

            $data = [
                [
                    'run_id' => '',
                    'user_id' => '',
                    'username' => '',
                    'distance' => 0,
                    'duration' => 0,
                    'duration_formatted' => '00:00:00',
                    'average_speed' => 0,
                    'calories_burned' => 0,
                    'start_time' => '',
                    'end_time' => '',
                    'created_at' => $thaiDate
                ]
            ];
        }

        // สร้างชื่อไฟล์
        $filename = 'running_data_' . date('Y-m-d_His') . '.xls';

        // สร้าง HTML table ที่ Excel สามารถอ่านได้
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<table border="1">';

        // สร้างหัวตาราง
        $html .= '<tr>';
        foreach (array_keys($data[0]) as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        $html .= '</tr>';

        // สร้างข้อมูลแถว
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $value) {
                $html .= '<td>' . $value . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        // กำหนด response headers
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return response($html, 200, $headers);
    }

    /**
     * แสดงสถานที่ออกกำลังกาย
     */
    public function locations()
    {
        // ดึงข้อมูลสถานที่ออกกำลังกายที่ได้รับความนิยม
        $popularLocations = DB::table('tb_run')
            ->select(DB::raw('COUNT(*) as run_count'))
            ->where('is_completed', true)
            ->whereNotNull('route_data')
            ->count();

        // สร้างข้อมูลการวิ่งตามจังหวัด/เขต (ตัวอย่างเท่านั้น - ควรดึงจากฐานข้อมูลจริง)
        $runsByArea = [
            ['name' => 'กรุงเทพมหานคร', 'count' => rand(50, 200)],
            ['name' => 'นนทบุรี', 'count' => rand(20, 100)],
            ['name' => 'ปทุมธานี', 'count' => rand(10, 80)],
            ['name' => 'เชียงใหม่', 'count' => rand(30, 120)],
            ['name' => 'ภูเก็ต', 'count' => rand(40, 150)],
        ];

        // ข้อมูลผู้ใช้ที่มีการวิ่งมากที่สุดในพื้นที่ต่างๆ
        $topRunnersInAreas = User::select(
                'tb_user.user_id',
                'tb_user.username',
                'tb_user.firstname',
                'tb_user.lastname',
                DB::raw('COUNT(tb_run.run_id) as run_count'),
                DB::raw('SUM(tb_run.distance) as total_distance')
            )
            ->join('tb_run', 'tb_user.user_id', '=', 'tb_run.user_id')
            ->where('tb_run.is_completed', true)
            ->groupBy('tb_user.user_id', 'tb_user.username', 'tb_user.firstname', 'tb_user.lastname')
            ->orderBy('total_distance', 'desc')
            ->take(10)
            ->get();

        return view('admin.run.locations', compact(
            'popularLocations',
            'runsByArea',
            'topRunnersInAreas'
        ));
    }
}


