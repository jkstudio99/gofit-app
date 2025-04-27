<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบว่ามีตาราง tb_event หรือไม่
        if (Schema::hasTable('tb_event')) {
            // ข้อมูลตัวอย่างสำหรับกิจกรรม
            $events = [
                [
                    'event_name' => 'การวิ่งมินิมาราธอนเพื่อสุขภาพ',
                    'event_desc' => 'กิจกรรมวิ่งมินิมาราธอนเพื่อส่งเสริมสุขภาพในชุมชน ระยะทาง 10 กิโลเมตร',
                    'location' => 'สวนสาธารณะเฉลิมพระเกียรติ',
                    'start_datetime' => Carbon::now()->addDays(15)->setHour(6)->setMinute(0),
                    'end_datetime' => Carbon::now()->addDays(15)->setHour(10)->setMinute(0),
                    'event_image' => 'events/marathon.jpg',
                    'distance' => 10.0,
                    'max_participants' => 100,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'event_name' => 'วิ่งการกุศลเพื่อผู้ป่วยโรคหัวใจ',
                    'event_desc' => 'กิจกรรมวิ่งเพื่อระดมทุนช่วยเหลือผู้ป่วยโรคหัวใจ ระยะทาง 5 กิโลเมตร',
                    'location' => 'อุทยานประวัติศาสตร์',
                    'start_datetime' => Carbon::now()->addDays(30)->setHour(5)->setMinute(30),
                    'end_datetime' => Carbon::now()->addDays(30)->setHour(9)->setMinute(0),
                    'event_image' => 'events/charity_run.jpg',
                    'distance' => 5.0,
                    'max_participants' => 200,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'event_name' => 'วิ่งท้าทายความท้าทาย',
                    'event_desc' => 'กิจกรรมวิ่งพิชิตเส้นทางท้าทายในเมือง ระยะทาง 15 กิโลเมตร',
                    'location' => 'สวนสาธารณะกลางเมือง',
                    'start_datetime' => Carbon::now()->addDays(7)->setHour(5)->setMinute(0),
                    'end_datetime' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0),
                    'event_image' => 'events/challenge_run.jpg',
                    'distance' => 15.0,
                    'max_participants' => 50,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'event_name' => 'วิ่งมาราธอนชิงถ้วยพระราชทาน',
                    'event_desc' => 'การแข่งขันวิ่งมาราธอนประจำปี ชิงถ้วยพระราชทาน ระยะทาง 42.195 กิโลเมตร',
                    'location' => 'สนามกีฬาแห่งชาติ',
                    'start_datetime' => Carbon::now()->addMonths(2)->setHour(4)->setMinute(0),
                    'end_datetime' => Carbon::now()->addMonths(2)->setHour(12)->setMinute(0),
                    'event_image' => 'events/marathon_cup.jpg',
                    'distance' => 42.195,
                    'max_participants' => 500,
                    'status' => 'draft',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'event_name' => 'กิจกรรมวิ่งสานสัมพันธ์ครอบครัว',
                    'event_desc' => 'กิจกรรมวิ่งเพื่อสานสัมพันธ์ในครอบครัว ระยะทาง 3 กิโลเมตร เหมาะสำหรับทุกเพศทุกวัย',
                    'location' => 'สวนสุขภาพชุมชน',
                    'start_datetime' => Carbon::now()->addDays(20)->setHour(16)->setMinute(0),
                    'end_datetime' => Carbon::now()->addDays(20)->setHour(19)->setMinute(0),
                    'event_image' => 'events/family_run.jpg',
                    'distance' => 3.0,
                    'max_participants' => 150,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];

            // เพิ่มข้อมูลกิจกรรม
            foreach ($events as $event) {
                DB::table('tb_event')->insert($event);
            }

            // แจ้งผลการทำงาน
            if (isset($this->command)) {
                $this->command->info('เพิ่มข้อมูลตัวอย่างกิจกรรมสำเร็จแล้ว จำนวน '.count($events).' รายการ');
            }
        } else if (Schema::hasTable('events')) {
            // ข้อมูลตัวอย่างสำหรับตาราง events (ถ้ามี)
            $events = [
                [
                    'title' => 'การวิ่งมินิมาราธอนเพื่อสุขภาพ',
                    'description' => 'กิจกรรมวิ่งมินิมาราธอนเพื่อส่งเสริมสุขภาพในชุมชน ระยะทาง 10 กิโลเมตร',
                    'location' => 'สวนสาธารณะเฉลิมพระเกียรติ',
                    'start_datetime' => Carbon::now()->addDays(15)->setHour(6)->setMinute(0),
                    'end_datetime' => Carbon::now()->addDays(15)->setHour(10)->setMinute(0),
                    'capacity' => 100,
                    'image_url' => 'events/marathon.jpg',
                    'created_by' => 1, // ต้องมีผู้ใช้ ID 1 ในระบบ
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'title' => 'วิ่งการกุศลเพื่อผู้ป่วยโรคหัวใจ',
                    'description' => 'กิจกรรมวิ่งเพื่อระดมทุนช่วยเหลือผู้ป่วยโรคหัวใจ ระยะทาง 5 กิโลเมตร',
                    'location' => 'อุทยานประวัติศาสตร์',
                    'start_datetime' => Carbon::now()->addDays(30)->setHour(5)->setMinute(30),
                    'end_datetime' => Carbon::now()->addDays(30)->setHour(9)->setMinute(0),
                    'capacity' => 200,
                    'image_url' => 'events/charity_run.jpg',
                    'created_by' => 1,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'title' => 'วิ่งท้าทายความท้าทาย',
                    'description' => 'กิจกรรมวิ่งพิชิตเส้นทางท้าทายในเมือง ระยะทาง 15 กิโลเมตร',
                    'location' => 'สวนสาธารณะกลางเมือง',
                    'start_datetime' => Carbon::now()->addDays(7)->setHour(5)->setMinute(0),
                    'end_datetime' => Carbon::now()->addDays(7)->setHour(10)->setMinute(0),
                    'capacity' => 50,
                    'image_url' => 'events/challenge_run.jpg',
                    'created_by' => 1,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'title' => 'วิ่งมาราธอนชิงถ้วยพระราชทาน',
                    'description' => 'การแข่งขันวิ่งมาราธอนประจำปี ชิงถ้วยพระราชทาน ระยะทาง 42.195 กิโลเมตร',
                    'location' => 'สนามกีฬาแห่งชาติ',
                    'start_datetime' => Carbon::now()->addMonths(2)->setHour(4)->setMinute(0),
                    'end_datetime' => Carbon::now()->addMonths(2)->setHour(12)->setMinute(0),
                    'capacity' => 500,
                    'image_url' => 'events/marathon_cup.jpg',
                    'created_by' => 1,
                    'status' => 'draft',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'title' => 'กิจกรรมวิ่งสานสัมพันธ์ครอบครัว',
                    'description' => 'กิจกรรมวิ่งเพื่อสานสัมพันธ์ในครอบครัว ระยะทาง 3 กิโลเมตร เหมาะสำหรับทุกเพศทุกวัย',
                    'location' => 'สวนสุขภาพชุมชน',
                    'start_datetime' => Carbon::now()->addDays(20)->setHour(16)->setMinute(0),
                    'end_datetime' => Carbon::now()->addDays(20)->setHour(19)->setMinute(0),
                    'capacity' => 150,
                    'image_url' => 'events/family_run.jpg',
                    'created_by' => 1,
                    'status' => 'published',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ];

            // เพิ่มข้อมูลกิจกรรม
            foreach ($events as $event) {
                DB::table('events')->insert($event);
            }

            // แจ้งผลการทำงาน
            if (isset($this->command)) {
                $this->command->info('เพิ่มข้อมูลตัวอย่างกิจกรรมสำเร็จแล้ว จำนวน '.count($events).' รายการ');
            }
        }
    }
}
