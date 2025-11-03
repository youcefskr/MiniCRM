<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

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

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
