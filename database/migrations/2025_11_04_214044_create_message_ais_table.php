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
        Schema::create('message_ais', function (Blueprint $table) {
             // 'client', 'ai', or 'employee'
            $table->string('sender')->default('client');   

            // Message text
            $table->text('content');

            // Optional link to a client or conversation (for CRM use)
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_ais');
    }
};
