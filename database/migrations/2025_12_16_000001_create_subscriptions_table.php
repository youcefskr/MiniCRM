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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de l'abonnement
            $table->text('description')->nullable(); // Description du service
            $table->foreignId('contact_id')->constrained()->onDelete('cascade'); // Client lié
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('set null'); // Opportunité liée
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Commercial responsable
            
            // Type et cycle
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annual', 'annual'])->default('monthly');
            $table->decimal('amount', 12, 2); // Montant HT
            $table->decimal('tax_rate', 5, 2)->default(19.00); // Taux de TVA (19% par défaut en Algérie)
            $table->string('currency', 3)->default('DZD'); // Devise
            
            // Dates
            $table->date('start_date'); // Date de début
            $table->date('end_date')->nullable(); // Date de fin (null = illimité)
            $table->date('next_billing_date'); // Prochaine date de facturation
            $table->date('next_renewal_date')->nullable(); // Prochaine date de renouvellement
            
            // Statut
            $table->enum('status', ['active', 'pending', 'paused', 'cancelled', 'expired'])->default('pending');
            $table->boolean('auto_renew')->default(true); // Renouvellement automatique
            $table->integer('renewal_reminder_days')->default(7); // Jours avant alerte renouvellement
            
            // Compteurs
            $table->integer('billing_count')->default(0); // Nombre de facturations effectuées
            $table->decimal('total_billed', 12, 2)->default(0); // Total facturé
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('terms')->nullable(); // Conditions particulières
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['status', 'next_billing_date']);
            $table->index(['contact_id', 'status']);
            $table->index('next_renewal_date');
        });
        
        // Table pivot pour les produits/services inclus dans l'abonnement
        Schema::create('subscription_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2); // Prix unitaire au moment de la souscription
            $table->decimal('discount', 5, 2)->default(0); // Remise en pourcentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_product');
        Schema::dropIfExists('subscriptions');
    }
};
