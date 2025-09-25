<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('assignment_id'); // Primary Key
            $table->foreignId('hr_liaison_id')
                  ->constrained('users', 'id')
                  ->onDelete('cascade'); // HR Liaison User
            $table->foreignId('grievance_id')
                  ->constrained('grievances', 'grievance_id')
                  ->onDelete('cascade'); // Grievance
            $table->foreignId('department_id')
                  ->constrained('departments', 'department_id')
                  ->onDelete('cascade'); // Department
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps(); // optional created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
