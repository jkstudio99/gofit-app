<?php

namespace App\Services;

use App\Models\OnboardingTour;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OnboardingService
{
    /**
     * เช็คสถานะของทัวร์
     *
     * @param string $tourKey
     * @return array
     */
    public function getTourStatus(string $tourKey): array
    {
        $user = Auth::user();
        if (!$user) {
            return ['shouldShow' => false];
        }

        $tour = OnboardingTour::firstOrCreate(
            ['user_id' => $user->user_id, 'tour_key' => $tourKey],
            ['status' => 'pending', 'show_again' => false]
        );

        // ถ้าเป็นผู้ใช้ใหม่ (login_count <= 1) ให้แสดงทัวร์โดยอัตโนมัติ
        $isNewUser = $user->login_count <= 1;

        return [
            'shouldShow' => $isNewUser || $tour->isPending() || $tour->show_again,
            'tourKey' => $tourKey,
            'status' => $tour->status,
            'isNewUser' => $isNewUser,
        ];
    }

    /**
     * อัปเดตสถานะของทัวร์
     *
     * @param string $tourKey
     * @param string $status
     * @param bool $showAgain
     * @return array
     */
    public function updateTourStatus(string $tourKey, string $status, bool $showAgain = false): array
    {
        $user = Auth::user();
        if (!$user) {
            return ['success' => false, 'message' => 'User not authenticated'];
        }

        $tour = OnboardingTour::firstOrCreate(
            ['user_id' => $user->user_id, 'tour_key' => $tourKey],
            ['status' => 'pending', 'show_again' => false]
        );

        if ($status === 'completed') {
            $tour->markAsCompleted();
        } elseif ($status === 'skipped') {
            $tour->markAsSkipped();
        }

        $tour->show_again = $showAgain;
        $tour->save();

        return [
            'success' => true,
            'tourKey' => $tourKey,
            'status' => $tour->status,
            'showAgain' => $tour->show_again,
        ];
    }

    /**
     * รีเซ็ตทัวร์ทั้งหมดของผู้ใช้
     *
     * @return array
     */
    public function resetAllTours(): array
    {
        $user = Auth::user();
        if (!$user) {
            return ['success' => false, 'message' => 'User not authenticated'];
        }

        // อัพเดทสถานะของทัวร์ทั้งหมดให้กลับมาเป็น pending
        OnboardingTour::where('user_id', $user->user_id)
            ->update([
                'status' => 'pending',
                'completed_at' => null,
                'show_again' => true,
            ]);

        return [
            'success' => true,
            'message' => 'All tours have been reset',
        ];
    }

    /**
     * ดึงข้อมูลการตั้งค่าทัวร์ของผู้ใช้
     *
     * @return array
     */
    public function getUserTourSettings(): array
    {
        $user = Auth::user();
        if (!$user) {
            return ['success' => false, 'message' => 'User not authenticated'];
        }

        // ดึงข้อมูลทัวร์ทั้งหมดของผู้ใช้
        $tours = OnboardingTour::where('user_id', $user->user_id)->get();

        // กำหนดลำดับของทัวร์
        $tourOrder = ['dashboard', 'run', 'badges', 'rewards'];

        // สร้างทัวร์ที่ขาดหายไป
        foreach ($tourOrder as $tourKey) {
            if (!$tours->where('tour_key', $tourKey)->count()) {
                $newTour = OnboardingTour::create([
                    'user_id' => $user->user_id,
                    'tour_key' => $tourKey,
                    'status' => 'pending',
                    'show_again' => false
                ]);
                $tours->push($newTour);
            }
        }

        // เรียงลำดับทัวร์ตามที่กำหนด
        $sortedTours = collect([]);
        foreach ($tourOrder as $tourKey) {
            $tour = $tours->where('tour_key', $tourKey)->first();
            if ($tour) {
                $sortedTours->push($tour);
            }
        }

        // เพิ่มทัวร์อื่นๆ ที่ไม่ได้อยู่ในลำดับที่กำหนด
        foreach ($tours as $tour) {
            if (!in_array($tour->tour_key, $tourOrder)) {
                $sortedTours->push($tour);
            }
        }

        return [
            'success' => true,
            'tours' => $sortedTours,
        ];
    }
}
