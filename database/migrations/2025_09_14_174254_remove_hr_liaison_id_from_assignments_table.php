<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['hr_liaison_id']); // remove FK if exists
            $table->dropColumn('hr_liaison_id');   // remove column
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('hr_liaison_id')->nullable()->after('assignment_id');
            $table->foreign('hr_liaison_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
