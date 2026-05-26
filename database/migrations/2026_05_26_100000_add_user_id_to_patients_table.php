<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        foreach (DB::table('patients')->orderBy('id')->get() as $patient) {
            $userId = DB::table('users')
                ->where('email', $patient->email)
                ->where('role', 'patient')
                ->value('id');

            if ($userId) {
                DB::table('patients')->where('id', $patient->id)->update(['user_id' => $userId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
