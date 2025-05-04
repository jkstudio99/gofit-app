<?php

namespace App\Http\Controllers;

use App\Models\Redeem;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RedeemController extends Controller
{
    /**
     * Display user's redemption history
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Redeem::with('reward')
                 ->where('user_id', $user->user_id);

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Apply sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'points-high':
                    $query->join('tb_reward', 'tb_redeem.reward_id', '=', 'tb_reward.reward_id')
                          ->orderBy('tb_reward.points_required', 'desc')
                          ->select('tb_redeem.*');
                    break;
                case 'points-low':
                    $query->join('tb_reward', 'tb_redeem.reward_id', '=', 'tb_reward.reward_id')
                          ->orderBy('tb_reward.points_required', 'asc')
                          ->select('tb_redeem.*');
                    break;
                default: // newest
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }

        $redeems = $query->get();

        return view('rewards.history', compact('redeems'));
    }

    /**
     * Create a new redemption
     */
    public function store(Request $request)
    {
        // Implementation will be in RewardController::redeem method
    }

    /**
     * Cancel a pending redemption
     */
    public function cancel(Redeem $redeem)
    {
        $user = Auth::user();

        // Check if the redeem belongs to the user
        if ($redeem->user_id != $user->user_id) {
            return redirect()->route('rewards.history')->with('error', 'คุณไม่มีสิทธิ์ยกเลิกรายการนี้');
        }

        // Check if the redeem is still in pending status
        if ($redeem->status !== 'pending') {
            return redirect()->route('rewards.history')->with('error', 'สามารถยกเลิกได้เฉพาะรายการที่มีสถานะรอดำเนินการเท่านั้น');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Get points back
            $pointsUsed = $redeem->points_used ?? $redeem->reward->points_required;

            User::where('user_id', $user->user_id)
                ->increment('points', $pointsUsed);

            // Increase reward quantity
            Reward::where('reward_id', $redeem->reward_id)
                  ->increment('quantity', 1);

            // Update redeem status
            $redeem->status = 'cancelled';
            $redeem->note = 'ยกเลิกโดยผู้ใช้';
            $redeem->save();

            DB::commit();
            return redirect()->route('rewards.history')->with('success', 'ยกเลิกการแลกรางวัลเรียบร้อยแล้ว คุณได้รับคะแนนคืน ' . $pointsUsed . ' คะแนน');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('rewards.history')->with('error', 'เกิดข้อผิดพลาดในการยกเลิกรายการ: ' . $e->getMessage());
        }
    }

    /**
     * Admin panel: Display a listing of redemptions
     */
    public function adminIndex(Request $request)
    {
        $query = Redeem::with(['user', 'reward']);

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            })->orWhereHas('reward', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Sort options
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['created_at', 'status'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        $query->orderBy($sortField, $sortDirection);

        $redeems = $query->paginate(10);

        return view('admin.redeems.index', compact('redeems'));
    }

    /**
     * Admin panel: Update redemption status
     */
    public function updateStatus(Request $request, $redeemId)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
            'note' => 'nullable|string',
        ]);

        // ค้นหา Redeem โดยตรงด้วย ID แทนการใช้ route model binding
        $redeem = Redeem::findOrFail($redeemId);

        $redeem->status = $request->status;

        if ($request->has('note')) {
            $redeem->note = $request->note;
        }

        $redeem->save();

        return redirect()->route('admin.redeems')->with('success', 'อัปเดตสถานะการแลกรางวัลเรียบร้อยแล้ว');
    }

    /**
     * Display the specified redemption
     */
    public function show(Redeem $redeem)
    {
        $user = Auth::user();

        // Check if the redeem belongs to the user
        if ($redeem->user_id != $user->user_id) {
            return redirect()->route('rewards.history')->with('error', 'คุณไม่มีสิทธิ์ดูรายการนี้');
        }

        return view('rewards.show', compact('redeem'));
    }
}
