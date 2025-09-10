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
        // Remove profile_pic from user_infos
        Schema::table('user_infos', function (Blueprint $table) {
            $table->dropColumn('profile_pic');
        });

        // Add profile_pic to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_pic')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
      public function down(): void
    {
        // Add profile_pic back to user_infos (rollback)
        Schema::table('user_infos', function (Blueprint $table) {
            $table->string('profile_pic')->nullable()->after('birthdate');
        });

        // Remove profile_pic from users (rollback)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_pic');
        });
    }
};
