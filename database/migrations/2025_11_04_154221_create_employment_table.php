<?php

declare(strict_types=1);

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
        Schema::create('employments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table
                ->foreignId('client_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->string('position');
            $table->date('hire_date');
            $table
                ->enum('status', ['active', 'inactive', 'terminated'])
                ->default('active');
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('work_location')->nullable();
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'effective_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};
