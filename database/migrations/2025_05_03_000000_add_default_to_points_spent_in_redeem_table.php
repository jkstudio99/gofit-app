<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_redeem', function (Blueprint $table) {
            // Change points_spent to allow NULL temporarily
            $table->integer('points_spent')->nullable()->change();
        });

        // Update any existing records that have NULL points_spent
        DB::statement('UPDATE tb_redeem SET points_spent = (
            SELECT points_required FROM tb_reward WHERE tb_reward.reward_id = tb_redeem.reward_id
        ) WHERE points_spent IS NULL');

        Schema::table('tb_redeem', function (Blueprint $table) {
            // Make points_spent NOT NULL again
            $table->integer('points_spent')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert as we're just adding missing data
    }
};
