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
        Schema::table('groups', function (Blueprint $table) {
            $table->text('comment')->nullable();
            $table->text('type')->nullable()->change();
            $table->foreignId('address_id')->nullable()->change();
            $table->foreignId('captain_id')->nullable()->change();
            $table->foreignId('territory_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['comment']);
        });
    }
};
