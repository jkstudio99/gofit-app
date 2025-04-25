<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'คุณต้องยอมรับ :attribute',
    'accepted_if' => 'คุณต้องยอมรับ :attribute เมื่อ :other เป็น :value',
    'active_url' => ':attribute ไม่ใช่ URL ที่ถูกต้อง',
    'after' => ':attribute ต้องเป็นวันที่หลังจาก :date',
    'after_or_equal' => ':attribute ต้องเป็นวันที่หลังจากหรือเท่ากับ :date',
    'alpha' => ':attribute ต้องประกอบด้วยตัวอักษรเท่านั้น',
    'alpha_dash' => ':attribute ต้องประกอบด้วยตัวอักษร ตัวเลข ขีดกลาง และขีดล่างเท่านั้น',
    'alpha_num' => ':attribute ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
    'array' => ':attribute ต้องเป็นอาร์เรย์',
    'before' => ':attribute ต้องเป็นวันที่ก่อน :date',
    'before_or_equal' => ':attribute ต้องเป็นวันที่ก่อนหรือเท่ากับ :date',
    'between' => [
        'numeric' => ':attribute ต้องอยู่ระหว่าง :min และ :max',
        'file' => ':attribute ต้องมีขนาดระหว่าง :min และ :max กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวระหว่าง :min และ :max ตัวอักษร',
        'array' => ':attribute ต้องมีจำนวนระหว่าง :min และ :max รายการ',
    ],
    'boolean' => ':attribute ต้องเป็น true หรือ false เท่านั้น',
    'confirmed' => 'การยืนยัน :attribute ไม่ตรงกัน',
    'current_password' => 'รหัสผ่านไม่ถูกต้อง',
    'date' => ':attribute ไม่ใช่วันที่ที่ถูกต้อง',
    'date_equals' => ':attribute ต้องเป็นวันที่เท่ากับ :date',
    'date_format' => ':attribute ไม่ตรงกับรูปแบบ :format',
    'declined' => ':attribute ต้องถูกปฏิเสธ',
    'declined_if' => ':attribute ต้องถูกปฏิเสธเมื่อ :other เป็น :value',
    'different' => ':attribute และ :other ต้องแตกต่างกัน',
    'digits' => ':attribute ต้องเป็น :digits หลัก',
    'digits_between' => ':attribute ต้องมีความยาวระหว่าง :min และ :max หลัก',
    'dimensions' => ':attribute มีขนาดภาพที่ไม่ถูกต้อง',
    'distinct' => ':attribute มีค่าที่ซ้ำกัน',
    'email' => ':attribute ต้องเป็นที่อยู่อีเมลที่ถูกต้อง',
    'ends_with' => ':attribute ต้องลงท้ายด้วย: :values',
    'enum' => ':attribute ที่เลือกไม่ถูกต้อง',
    'exists' => ':attribute ที่เลือกไม่ถูกต้อง',
    'file' => ':attribute ต้องเป็นไฟล์',
    'filled' => ':attribute ต้องมีค่า',
    'gt' => [
        'numeric' => ':attribute ต้องมีค่ามากกว่า :value',
        'file' => ':attribute ต้องมีขนาดมากกว่า :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวมากกว่า :value ตัวอักษร',
        'array' => ':attribute ต้องมีมากกว่า :value รายการ',
    ],
    'gte' => [
        'numeric' => ':attribute ต้องมีค่ามากกว่าหรือเท่ากับ :value',
        'file' => ':attribute ต้องมีขนาดมากกว่าหรือเท่ากับ :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวมากกว่าหรือเท่ากับ :value ตัวอักษร',
        'array' => ':attribute ต้องมีอย่างน้อย :value รายการ',
    ],
    'image' => ':attribute ต้องเป็นรูปภาพ',
    'in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'in_array' => ':attribute ไม่มีอยู่ใน :other',
    'integer' => ':attribute ต้องเป็นจำนวนเต็ม',
    'ip' => ':attribute ต้องเป็นที่อยู่ IP ที่ถูกต้อง',
    'ipv4' => ':attribute ต้องเป็นที่อยู่ IPv4 ที่ถูกต้อง',
    'ipv6' => ':attribute ต้องเป็นที่อยู่ IPv6 ที่ถูกต้อง',
    'json' => ':attribute ต้องเป็นสตริง JSON ที่ถูกต้อง',
    'lt' => [
        'numeric' => ':attribute ต้องมีค่าน้อยกว่า :value',
        'file' => ':attribute ต้องมีขนาดน้อยกว่า :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวน้อยกว่า :value ตัวอักษร',
        'array' => ':attribute ต้องมีน้อยกว่า :value รายการ',
    ],
    'lte' => [
        'numeric' => ':attribute ต้องมีค่าน้อยกว่าหรือเท่ากับ :value',
        'file' => ':attribute ต้องมีขนาดน้อยกว่าหรือเท่ากับ :value กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวน้อยกว่าหรือเท่ากับ :value ตัวอักษร',
        'array' => ':attribute ต้องไม่มีมากกว่า :value รายการ',
    ],
    'mac_address' => ':attribute ต้องเป็นที่อยู่ MAC ที่ถูกต้อง',
    'max' => [
        'numeric' => ':attribute ต้องไม่มากกว่า :max',
        'file' => ':attribute ต้องมีขนาดไม่เกิน :max กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวไม่เกิน :max ตัวอักษร',
        'array' => ':attribute ต้องไม่มีมากกว่า :max รายการ',
    ],
    'mimes' => ':attribute ต้องเป็นไฟล์ประเภท: :values',
    'mimetypes' => ':attribute ต้องเป็นไฟล์ประเภท: :values',
    'min' => [
        'numeric' => ':attribute ต้องมีค่าอย่างน้อย :min',
        'file' => ':attribute ต้องมีขนาดอย่างน้อย :min กิโลไบต์',
        'string' => ':attribute ต้องมีความยาวอย่างน้อย :min ตัวอักษร',
        'array' => ':attribute ต้องมีอย่างน้อย :min รายการ',
    ],
    'multiple_of' => ':attribute ต้องเป็นตัวคูณของ :value',
    'not_in' => ':attribute ที่เลือกไม่ถูกต้อง',
    'not_regex' => 'รูปแบบของ :attribute ไม่ถูกต้อง',
    'numeric' => ':attribute ต้องเป็นตัวเลข',
    'password' => 'รหัสผ่านไม่ถูกต้อง',
    'present' => ':attribute ต้องมีอยู่',
    'prohibited' => ':attribute ไม่ได้รับอนุญาต',
    'prohibited_if' => ':attribute ไม่ได้รับอนุญาตเมื่อ :other เป็น :value',
    'prohibited_unless' => ':attribute ไม่ได้รับอนุญาตเว้นแต่ :other อยู่ใน :values',
    'prohibits' => ':attribute ไม่อนุญาตให้ :other มีอยู่',
    'regex' => 'รูปแบบของ :attribute ไม่ถูกต้อง',
    'required' => 'กรุณากรอก :attribute',
    'required_array_keys' => ':attribute ต้องมีรายการสำหรับ: :values',
    'required_if' => ':attribute จำเป็นเมื่อ :other เป็น :value',
    'required_unless' => ':attribute จำเป็นเว้นแต่ :other อยู่ใน :values',
    'required_with' => ':attribute จำเป็นเมื่อ :values มีอยู่',
    'required_with_all' => ':attribute จำเป็นเมื่อ :values มีอยู่ทั้งหมด',
    'required_without' => ':attribute จำเป็นเมื่อ :values ไม่มีอยู่',
    'required_without_all' => ':attribute จำเป็นเมื่อไม่มี :values อยู่เลย',
    'same' => ':attribute และ :other ต้องตรงกัน',
    'size' => [
        'numeric' => ':attribute ต้องเป็น :size',
        'file' => ':attribute ต้องมีขนาด :size กิโลไบต์',
        'string' => ':attribute ต้องมีความยาว :size ตัวอักษร',
        'array' => ':attribute ต้องมี :size รายการ',
    ],
    'starts_with' => ':attribute ต้องขึ้นต้นด้วย: :values',
    'string' => ':attribute ต้องเป็นสตริง',
    'timezone' => ':attribute ต้องเป็นเขตเวลาที่ถูกต้อง',
    'unique' => ':attribute นี้มีอยู่แล้ว',
    'uploaded' => ':attribute อัปโหลดล้มเหลว',
    'url' => ':attribute ต้องเป็น URL ที่ถูกต้อง',
    'uuid' => ':attribute ต้องเป็น UUID ที่ถูกต้อง',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'username' => [
            'required' => 'กรุณากรอกชื่อผู้ใช้',
            'unique' => 'ชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาเลือกชื่อผู้ใช้อื่น',
            'min' => 'ชื่อผู้ใช้ต้องมีอย่างน้อย :min ตัวอักษร',
        ],
        'password' => [
            'required' => 'กรุณากรอกรหัสผ่าน',
            'min' => 'รหัสผ่านต้องมีอย่างน้อย :min ตัวอักษร',
            'confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
        ],
        'email' => [
            'required' => 'กรุณากรอกอีเมล',
            'email' => 'กรุณากรอกอีเมลที่ถูกต้อง',
            'unique' => 'อีเมลนี้มีอยู่แล้ว กรุณาใช้อีเมลอื่น',
        ],
        'firstname' => [
            'required' => 'กรุณากรอกชื่อ',
        ],
        'lastname' => [
            'required' => 'กรุณากรอกนามสกุล',
        ],
        'telephone' => [
            'required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'regex' => 'เบอร์โทรศัพท์ไม่ถูกต้อง',
        ],
        'accept_terms' => [
            'required' => 'กรุณายอมรับเงื่อนไขการใช้งานและนโยบายความเป็นส่วนตัว',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'username' => 'ชื่อผู้ใช้',
        'password' => 'รหัสผ่าน',
        'email' => 'อีเมล',
        'firstname' => 'ชื่อ',
        'lastname' => 'นามสกุล',
        'telephone' => 'เบอร์โทรศัพท์',
        'accept_terms' => 'เงื่อนไขการใช้งาน',
    ],

];
