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
        Schema::table('interactions', function (Blueprint $table) {
            $table->dateTime('date_interaction')->nullable()->after('type_id');
            $table->enum('statut', ['planifié', 'réalisé', 'annulé'])->default('réalisé')->after('date_interaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropColumn(['date_interaction', 'statut']);
        });
    }
};
