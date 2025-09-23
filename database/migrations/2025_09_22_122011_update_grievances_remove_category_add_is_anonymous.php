<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('grievances', function (Blueprint $table) {
        $table->dropColumn('category');
        $table->boolean('is_anonymous')->default(false)->after('grievance_details');
    });
}

public function down()
{
    Schema::table('grievances', function (Blueprint $table) {
        $table->string('category')->nullable()->after('grievance_details');
        $table->dropColumn('is_anonymous');
    });
}

};
