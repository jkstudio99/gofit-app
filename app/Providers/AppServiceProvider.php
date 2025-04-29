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
    }
}
