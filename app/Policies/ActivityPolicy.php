<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true; // ทุกคนสามารถดูรายการกิจกรรมได้
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Activity $activity)
    {
        // ผู้ใช้ที่สร้างกิจกรรมหรือ admin สามารถดูรายละเอียดได้
        // หรือผู้ใช้ทั่วไปสามารถดูกิจกรรมสาธารณะได้
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // เฉพาะ admin เท่านั้นที่สามารถสร้างกิจกรรมได้
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Activity $activity)
    {
        // เฉพาะ admin เท่านั้นที่สามารถแก้ไขกิจกรรมได้
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Activity $activity)
    {
        // เฉพาะ admin เท่านั้นที่สามารถลบกิจกรรมได้
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can register for the activity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function register(User $user, Activity $activity)
    {
        // ตรวจสอบว่าผู้ใช้ยังไม่ได้ลงทะเบียนกิจกรรมนี้
        // และกิจกรรมยังไม่เต็ม
        // และยังไม่หมดเวลาลงทะเบียน
        return true; // ตัวอย่างเริ่มต้น ควรมีการตรวจสอบเงื่อนไขตามความเหมาะสม
    }
}
