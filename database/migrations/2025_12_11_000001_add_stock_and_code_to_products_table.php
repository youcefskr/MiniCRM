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
        Schema::table('products', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('name');
            $table->integer('stock_quantity')->default(0)->after('price');
            $table->boolean('is_active')->default(true)->after('stock_quantity');
            $table->enum('type', ['product', 'service', 'subscription'])->default('product')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['code', 'stock_quantity', 'is_active', 'type']);
        });
    }
};
