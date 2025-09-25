<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grievance_attachments', function (Blueprint $table) {
            $table->id('attachment_id');
            $table->foreignId('grievance_id')->constrained('grievances', 'grievance_id')->onDelete('cascade');
            $table->string('file_path');  // store the path of the file
            $table->string('file_name');  // optional: store original file name
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievance_attachments');
    }
};
