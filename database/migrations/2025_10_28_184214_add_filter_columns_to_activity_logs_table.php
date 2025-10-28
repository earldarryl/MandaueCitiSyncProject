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
        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'module')) {
                $table->string('module')->nullable()->after('role_id')->index();
            }
            if (! Schema::hasColumn('activity_logs', 'action_type')) {
                $table->string('action_type')->nullable()->after('action')->index();
            }
            if (! Schema::hasColumn('activity_logs', 'model_type')) {
                $table->string('model_type')->nullable()->after('action_type')->index();
            }
            if (! Schema::hasColumn('activity_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type')->index();
            }

            if (! Schema::hasColumn('activity_logs', 'description')) {
                $table->text('description')->nullable()->after('model_id');
            }
            if (! Schema::hasColumn('activity_logs', 'changes')) {
                $table->json('changes')->nullable()->after('description');
            }

            if (! Schema::hasColumn('activity_logs', 'status')) {
                $table->string('status')->default('success')->after('changes')->index();
            }
            if (! Schema::hasColumn('activity_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('device_info');
            }
            if (! Schema::hasColumn('activity_logs', 'platform')) {
                $table->string('platform')->nullable()->after('user_agent')->index();
            }
            if (! Schema::hasColumn('activity_logs', 'location')) {
                $table->string('location')->nullable()->after('platform')->index();
            }

            if (! Schema::hasColumn('activity_logs', 'timestamp_index')) {
                $table->index('timestamp', 'activity_logs_timestamp_index');
            }

            if (! $this->indexExists('activity_logs', 'activity_logs_user_id_index')) {
                $table->index('user_id', 'activity_logs_user_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'module')) {
                $table->dropColumn('module');
            }
            if (Schema::hasColumn('activity_logs', 'action_type')) {
                $table->dropColumn('action_type');
            }
            if (Schema::hasColumn('activity_logs', 'model_type')) {
                $table->dropColumn('model_type');
            }
            if (Schema::hasColumn('activity_logs', 'model_id')) {
                $table->dropColumn('model_id');
            }
            if (Schema::hasColumn('activity_logs', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('activity_logs', 'changes')) {
                $table->dropColumn('changes');
            }
            if (Schema::hasColumn('activity_logs', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('activity_logs', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('activity_logs', 'platform')) {
                $table->dropColumn('platform');
            }
            if (Schema::hasColumn('activity_logs', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasTable('activity_logs')) {
                @DB::statement('DROP INDEX IF EXISTS activity_logs_timestamp_index ON activity_logs');
                if ($this->indexExists('activity_logs', 'activity_logs_user_id_index')) {
                    $table->dropIndex('activity_logs_user_id_index');
                }
            }
        });
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        try {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = array_keys($sm->listTableIndexes($table));
            return in_array($indexName, $indexes, true);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
