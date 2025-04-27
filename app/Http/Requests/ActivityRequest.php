<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // อนุญาตให้ผู้ใช้ที่เข้าสู่ระบบแล้วทำรายการได้
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'activity_type' => ['required', 'string', Rule::in(['running', 'walking', 'cycling', 'swimming', 'hiking', 'gym', 'yoga', 'other'])],
            'distance' => ['nullable', 'numeric', 'min:0'],
            'duration' => ['required', 'numeric', 'min:1'],
            'calories_burned' => ['nullable', 'numeric', 'min:0'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'heart_rate_avg' => ['nullable', 'integer', 'min:30', 'max:220'],
            'average_speed' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'route_gps_data' => ['nullable', 'json'],
            'is_test' => ['nullable', 'boolean']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'activity_type.required' => 'โปรดระบุประเภทกิจกรรม',
            'activity_type.in' => 'ประเภทกิจกรรมไม่ถูกต้อง',
            'distance.numeric' => 'ระยะทางต้องเป็นตัวเลข',
            'distance.min' => 'ระยะทางต้องมีค่ามากกว่าหรือเท่ากับ 0',
            'duration.required' => 'โปรดระบุระยะเวลา',
            'duration.numeric' => 'ระยะเวลาต้องเป็นตัวเลข',
            'duration.min' => 'ระยะเวลาต้องมีค่ามากกว่าหรือเท่ากับ 1 นาที',
            'calories_burned.numeric' => 'แคลอรี่ต้องเป็นตัวเลข',
            'calories_burned.min' => 'แคลอรี่ต้องมีค่ามากกว่าหรือเท่ากับ 0',
            'start_time.required' => 'โปรดระบุเวลาเริ่มต้น',
            'start_time.date' => 'รูปแบบเวลาเริ่มต้นไม่ถูกต้อง',
            'end_time.date' => 'รูปแบบเวลาสิ้นสุดไม่ถูกต้อง',
            'end_time.after' => 'เวลาสิ้นสุดต้องมาหลังเวลาเริ่มต้น',
            'heart_rate_avg.integer' => 'อัตราการเต้นของหัวใจต้องเป็นจำนวนเต็ม',
            'heart_rate_avg.min' => 'อัตราการเต้นของหัวใจต้องมีค่ามากกว่าหรือเท่ากับ 30',
            'heart_rate_avg.max' => 'อัตราการเต้นของหัวใจต้องมีค่าน้อยกว่าหรือเท่ากับ 220',
            'average_speed.numeric' => 'ความเร็วเฉลี่ยต้องเป็นตัวเลข',
            'average_speed.min' => 'ความเร็วเฉลี่ยต้องมีค่ามากกว่าหรือเท่ากับ 0',
            'notes.max' => 'บันทึกเพิ่มเติมต้องมีความยาวไม่เกิน 500 ตัวอักษร',
            'route_gps_data.json' => 'ข้อมูลเส้นทาง GPS ไม่ถูกต้อง'
        ];
    }
}
