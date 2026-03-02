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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('profile_image_path')->nullable()->after('user_id');
            $table->string('specialization')->nullable()->after('academy_name');
            $table->string('phone_number')->nullable()->after('bio');
            $table->json('social_links')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['profile_image_path', 'specialization', 'phone_number', 'social_links']);
        });
    }
};
