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
        Schema::table('shortened_urls', function (Blueprint $table) {
            $table->string('custom_slug', 100)->nullable()->after('short_code');
            $table->boolean('is_custom')->default(false)->after('custom_slug');
            
            // Add unique index for custom_slug when not null
            $table->unique('custom_slug', 'shortened_urls_custom_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shortened_urls', function (Blueprint $table) {
            $table->dropUnique('shortened_urls_custom_slug_unique');
            $table->dropColumn(['custom_slug', 'is_custom']);
        });
    }
};
