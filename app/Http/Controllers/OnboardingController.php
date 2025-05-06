<?php

namespace App\Http\Controllers;

use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OnboardingController extends Controller
{
    protected $onboardingService;

    /**
     * Create a new controller instance.
     *
     * @param OnboardingService $onboardingService
     * @return void
     */
    public function __construct(OnboardingService $onboardingService)
    {
        $this->middleware('auth');
        $this->onboardingService = $onboardingService;
    }

    /**
     * ตรวจสอบสถานะของทัวร์
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkTourStatus(Request $request): JsonResponse
    {
        $tourKey = $request->input('tour_key', 'dashboard');
        $result = $this->onboardingService->getTourStatus($tourKey);

        return response()->json($result);
    }

    /**
     * อัพเดทสถานะของทัวร์
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateTourStatus(Request $request): JsonResponse
    {
        $request->validate([
            'tour_key' => 'required|string',
            'status' => 'required|in:completed,skipped,pending',
            'show_again' => 'boolean',
        ]);

        $tourKey = $request->input('tour_key');
        $status = $request->input('status');
        $showAgain = $request->input('show_again', false);

        $result = $this->onboardingService->updateTourStatus($tourKey, $status, $showAgain);

        return response()->json($result);
    }

    /**
     * รีเซ็ตทัวร์ทั้งหมด
     *
     * @return JsonResponse
     */
    public function resetAllTours(): JsonResponse
    {
        $result = $this->onboardingService->resetAllTours();

        return response()->json($result);
    }

    /**
     * รับการตั้งค่าทัวร์ของผู้ใช้
     *
     * @return JsonResponse
     */
    public function getUserTourSettings(): JsonResponse
    {
        $result = $this->onboardingService->getUserTourSettings();

        return response()->json($result);
    }

    /**
     * แสดงหน้าตั้งค่าทัวร์ในโปรไฟล์
     *
     * @return \Illuminate\View\View
     */
    public function showTourSettings()
    {
        $result = $this->onboardingService->getUserTourSettings();
        $tours = $result['success'] ? $result['tours'] : [];

        return view('profile.tour-settings', compact('tours'));
    }
}
