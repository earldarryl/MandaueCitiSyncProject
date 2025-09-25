<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hr_liaison_department', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('hr_liaison_id');   // FK to users
            $table->unsignedBigInteger('department_id');   // FK to departments
            $table->timestamps();

            $table->foreign('hr_liaison_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('cascade');

            $table->unique(['hr_liaison_id', 'department_id']); // prevent duplicates
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_liaison_department');
    }
};
