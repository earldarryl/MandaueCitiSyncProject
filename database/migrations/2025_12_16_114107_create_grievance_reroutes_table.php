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
        Schema::create('grievance_reroutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grievance_id')->constrained('grievances', 'grievance_id')->onDelete('cascade');
            $table->foreignId('from_department_id')->nullable()->constrained('departments', 'department_id');
            $table->foreignId('to_department_id')->nullable()->constrained('departments', 'department_id');
            $table->foreignId('performed_by')->nullable()->constrained('users');
            $table->string('from_category')->nullable();
            $table->string('to_category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grievance_reroutes');
    }
};
