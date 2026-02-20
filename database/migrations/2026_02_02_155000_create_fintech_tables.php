<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Wallets (Portefeuilles)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            // MorphTarget: User (Client/Admin) or Enterprise (Vendeur)
            $table->morphs('holder');
            $table->decimal('balance', 15, 2)->default(0); // 15 chiffres, 2 décimales pour FCFA
            $table->string('currency', 3)->default('XOF'); // XOF = Franc CFA
            $table->boolean('is_frozen')->default(false); // Gel des fonds en cas de fraude suspectée
            $table->timestamps();

            $table->unique(['holder_id', 'holder_type']); // Un seul wallet par entité
        });

        // 2. Transactions (Ledger)
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID pour éviter l'énumération
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            
            $table->string('type'); // casted to TransactionType Enum
            $table->decimal('amount', 15, 2); // Positif (Crédit) ou Négatif (Débit)
            $table->decimal('fee', 10, 2)->default(0); // Frais de services
            
            $table->string('status')->default('pending'); // casted to TransactionStatus Enum
            $table->string('payment_method')->nullable(); // tmoney, flooz, stripe, wallet, system
            $table->string('reference_external')->nullable(); // ID transaction TMoney/Stripe
            $table->string('reference_unique')->unique(); // Notre réf unique pour idempotence (ex: TX-123456)
            
            $table->text('description')->nullable();
            $table->json('meta')->nullable(); // Données contextuelles (order_id, ip_address, etc.)
            
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            // Indexing for performance
            $table->index(['type', 'status']);
            $table->index('reference_external');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
    }
};
