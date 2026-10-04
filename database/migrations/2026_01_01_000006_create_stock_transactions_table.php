<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('stock_transactions', function (Blueprint $table) {
        $table->id();

        // ✅ foreignId يطابق نوع id تلقائياً — ينهي خطأ errno 150 نهائياً
        $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
        $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

        $table->string('transaction_type');
        $table->string('reference_type')->nullable();
        $table->unsignedBigInteger('reference_id')->nullable();

        $table->decimal('quantity', 15, 3);
        $table->enum('movement', ['in', 'out']);
        $table->text('notes')->nullable();

        $table->timestamps();

        $table->index(['item_id', 'warehouse_id']);
        $table->index('created_at');
        $table->index(['reference_type', 'reference_id']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};