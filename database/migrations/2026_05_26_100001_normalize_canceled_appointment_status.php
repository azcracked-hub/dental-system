<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('appointments')
            ->where('status', 'cancelled')
            ->update(['status' => 'canceled']);
    }

    public function down(): void
    {
        DB::table('appointments')
            ->where('status', 'canceled')
            ->update(['status' => 'cancelled']);
    }
};
