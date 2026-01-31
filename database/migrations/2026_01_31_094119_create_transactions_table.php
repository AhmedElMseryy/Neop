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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();

            $table->unsignedBigInteger('source_account_id');
            $table->foreign('source_account_id')->references('id')->on('accounts')->onDelete('cascade');

            $table->unsignedBigInteger('destination_account_id');
            $table->foreign('destination_account_id')->references('id')->on('accounts')->onDelete('cascade');

            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);

            $table->string('status');
            $table->string('type');

            $table->text('description')->nullable();

            $table->decimal('source_balance_before', 15, 2)->nullable();
            $table->decimal('source_balance_after', 15, 2)->nullable();
            $table->decimal('destination_balance_before', 15, 2)->nullable();
            $table->decimal('destination_balance_after', 15, 2)->nullable();

            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
