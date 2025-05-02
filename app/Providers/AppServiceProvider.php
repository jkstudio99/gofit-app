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
    }
}

