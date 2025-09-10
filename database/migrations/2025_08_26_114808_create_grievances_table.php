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
        Schema::create('grievances', function (Blueprint $table) {
            $table->bigIncrements('grievance_id');
            $table->unsignedBigInteger('user_id');
            $table->string('category', 100)->nullable();
            $table->enum('grievance_status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');
            $table->string('grievance_type', 100)->nullable();
            $table->integer('processing_days')->nullable();
            $table->string('grievance_title', 255);
            $table->text('grievance_details')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grievances');
    }
};
