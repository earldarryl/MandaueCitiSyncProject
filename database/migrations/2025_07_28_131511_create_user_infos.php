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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade'); // cleaner FK syntax
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable(); // optional for some users
            $table->string('last_name')->nullable();
            $table->string('suffix')->nullable(); // not everyone has a suffix
            $table->string('civil_status')->nullable();
            $table->string('barangay')->nullable();
            $table->string('sitio')->nullable();
            $table->date('birthdate')->nullable(); // better to use `date` instead of `string`
            $table->string('profile_pic')->nullable(); // profile pic may not always be set
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
