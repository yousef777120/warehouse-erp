<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique();
            $table->date('date');
            $table->string('description')->nullable();
            $table->enum('source', ['ai_invoice', 'payroll', 'warehouse', 'manual'])->default('manual');
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->boolean('by_agent')->default(false);
            $table->timestamps();
        });

        Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('account_id')->constrained();
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->json('extracted_data')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'posted'])->default('pending');
            $table->unsignedBigInteger('journal_entry_id')->nullable();
            $table->text('ai_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('job_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('function')->nullable();
            $table->decimal('salary', 10, 2);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_title_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('hire_date')->nullable();
            $table->decimal('salary', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('job_titles');
        Schema::dropIfExists('ai_invoices');
        Schema::dropIfExists('journal_lines');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounts');
    }
};