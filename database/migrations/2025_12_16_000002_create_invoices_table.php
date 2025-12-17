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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Numéro de facture (ex: INV-2024-0001)
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('opportunity_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Créateur
            
            // Montants
            $table->decimal('subtotal', 12, 2); // Sous-total HT
            $table->decimal('tax_amount', 12, 2)->default(0); // Montant TVA
            $table->decimal('discount_amount', 12, 2)->default(0); // Montant remise
            $table->decimal('total', 12, 2); // Total TTC
            $table->string('currency', 3)->default('DZD');
            
            // Dates
            $table->date('issue_date'); // Date d'émission
            $table->date('due_date'); // Date d'échéance
            $table->date('paid_date')->nullable(); // Date de paiement
            
            // Statut
            $table->enum('status', ['draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('amount_paid', 12, 2)->default(0);
            
            // Paiement
            $table->string('payment_method')->nullable(); // espèces, virement, chèque, etc.
            $table->string('payment_reference')->nullable(); // Référence du paiement
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            
            // Type
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_period')->nullable(); // monthly, quarterly, annual
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['status', 'due_date']);
            $table->index(['contact_id', 'status']);
        });
        
        // Table des lignes de facture
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->decimal('discount', 5, 2)->default(0); // En pourcentage
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
