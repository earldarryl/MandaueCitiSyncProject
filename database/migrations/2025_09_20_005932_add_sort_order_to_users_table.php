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
         Schema::table('users', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('id');
        });

        // Populate sort_order based on id
        DB::table('users')
            ->orderBy('id')
            ->get()
            ->each(function ($user, $index) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['sort_order' => $index + 1]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
