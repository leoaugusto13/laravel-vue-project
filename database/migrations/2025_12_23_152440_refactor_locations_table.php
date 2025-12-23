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
        Schema::table('locations', function (Blueprint $table) {
            // Drop Foreign Keys
            $table->dropForeign(['modality_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['regional_id']);

            // Drop Columns
            $table->dropColumn(['modality_id', 'city_id', 'regional_id', 'uaitec', 'ead']);

            // Add new column
            $table->string('name')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('name');

            $table->foreignId('modality_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->nullable()->constrained('cities');
            $table->foreignId('regional_id')->nullable()->constrained('regionals');
            $table->boolean('uaitec')->default(false);
            $table->boolean('ead')->default(false);
        });
    }
};
