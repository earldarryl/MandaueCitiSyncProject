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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('gender')->nullable();
            $table->string('region')->nullable();
            $table->string('service')->nullable();
            $table->string('cc1')->nullable();
            $table->string('cc2')->nullable();
            $table->string('cc3')->nullable();
            $table->json('answers')->nullable();
            $table->text('suggestions')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
