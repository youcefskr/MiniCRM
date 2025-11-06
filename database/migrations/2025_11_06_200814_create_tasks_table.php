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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority', ['basse', 'normale', 'haute'])->default('normale');
            $table->enum('status', ['en attente', 'en cours', 'terminee'])->default('en attente');

            // Clé étrangère pour l'utilisateur assigné (non-nullable, supprime les tâches si l'utilisateur est supprimé)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Clé étrangère pour le contact lié (nullable, met à NULL si le contact est supprimé)
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};