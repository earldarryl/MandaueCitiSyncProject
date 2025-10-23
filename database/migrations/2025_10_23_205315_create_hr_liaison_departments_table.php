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
        Schema::create('hr_liaison_departmentss', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hr_liaison_id');
            $table->unsignedBigInteger('department_id');
            $table->timestamps();

            $table->foreign('hr_liaison_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_liaison_departmentss');
    }
};
