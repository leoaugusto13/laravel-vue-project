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
        Schema::table('trainings', function (Blueprint $table) {
            $table->foreignId('training_type_id')->nullable()->constrained('training_types')->after('venue');
            $table->foreignId('target_audience_id')->nullable()->constrained('target_audiences')->after('training_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropForeign(['training_type_id']);
            $table->dropForeign(['target_audience_id']);
            $table->dropColumn(['training_type_id', 'target_audience_id']);
        });
    }
};
