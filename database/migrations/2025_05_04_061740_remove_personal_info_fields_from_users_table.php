<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            // Remove personal information fields
            $table->dropColumn([
                'weight',
                'height',
                'birthdate',
                'gender',
                'gmail_user_id',
                'facebook_user_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            // Add back personal information fields if needed
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('gender')->nullable();
            $table->string('gmail_user_id')->nullable();
            $table->string('facebook_user_id')->nullable();
        });
    }
};
