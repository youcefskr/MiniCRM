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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // Backup du nom en cas de suppression user
            $table->string('action'); // create, update, delete, login, logout, view, export, etc.
            $table->string('module'); // contacts, opportunities, products, users, etc.
            $table->string('model_type')->nullable(); // Nom de la classe du modèle
            $table->unsignedBigInteger('model_id')->nullable(); // ID de l'élément concerné
            $table->string('model_name')->nullable(); // Nom/titre de l'élément pour référence
            $table->text('description'); // Description lisible de l'action
            $table->json('old_values')->nullable(); // Valeurs avant modification
            $table->json('new_values')->nullable(); // Valeurs après modification
            $table->json('changed_fields')->nullable(); // Liste des champs modifiés
            $table->string('ip_address', 45)->nullable(); // IPv4 ou IPv6
            $table->string('user_agent')->nullable(); // Navigateur
            $table->string('url')->nullable(); // URL de l'action
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->boolean('is_sensitive')->default(false); // Action sensible = alerte
            $table->string('severity')->default('info'); // info, warning, danger
            $table->timestamps();

            // Index pour les recherches fréquentes
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action']);
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
            $table->index('is_sensitive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
