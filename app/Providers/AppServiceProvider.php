<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // เพิ่มบรรทัดนี้
use Carbon\Carbon; // เพิ่ม Carbon
use Carbon\CarbonInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191); // เพิ่มบรรทัดนี้

        // กำหนดค่า locale เริ่มต้นของ Carbon เป็นภาษาไทย
        Carbon::setLocale('th');

        // กำหนดรูปแบบวันที่ภาษาไทยแบบย่อ
        Carbon::macro('thaiDate', function () {
            /** @var Carbon|CarbonInterface $this */
            return $this->locale('th')->addYears(543)->isoFormat('D MMM YY HH.mm');
        });

        // กำหนดค่ารูปแบบวันที่และเวลาเริ่มต้นสำหรับแสดงผล
        Carbon::setToStringFormat('d M Y H:i');

        // Set Bangkok timezone as default
        config(['app.timezone' => 'Asia/Bangkok']);
        date_default_timezone_set('Asia/Bangkok');

        // Add Thai date formatter
        \Carbon\Carbon::macro('formatThaiDate', function ($showTime = true) {
            /** @var \Carbon\Carbon $this */
            $thaiMonths = [
                'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
            ];

            // Convert to Buddhist Era (BE) by adding 543 years
            $year = (int)$this->format('Y') + 543;

            // Get the short month name in Thai
            $month = $thaiMonths[(int)$this->format('n') - 1];

            if ($showTime) {
                return $this->format('j') . ' ' . $month . ' ' . $year . ' ' . $this->format('H:i') . ' น.';
            } else {
                return $this->format('j') . ' ' . $month . ' ' . $year;
            }
        });

        // Add flexible Thai format macro
        \Carbon\Carbon::macro('thaiFormat', function ($format = 'j M y H:i') {
            /** @var \Carbon\Carbon $this */
            $thaiMonths = [
                1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
            ];

            $thaiMonthsFull = [
                1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];

            $year = $this->year;
            $thaiYear = $year + 543;
            $monthNumber = $this->month;

            $result = str_replace(
                ['Y', 'y', 'F', 'M', 'j', 'd', 'H', 'i'],
                [
                    $thaiYear,                  // Y: Thai year (full)
                    substr($thaiYear, -2),      // y: Thai year (short)
                    $thaiMonthsFull[$monthNumber], // F: Thai month (full)
                    $thaiMonths[$monthNumber],  // M: Thai month (short)
                    $this->day,                 // j: Day without leading zeros
                    str_pad($this->day, 2, '0', STR_PAD_LEFT), // d: Day with leading zeros
                    $this->hour,                // H: Hours
                    str_pad($this->minute, 2, '0', STR_PAD_LEFT) // i: Minutes
                ],
                $format
            );

            return $result;
        });
    }
}

