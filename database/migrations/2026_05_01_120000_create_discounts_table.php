<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('amount', 10, 2);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_automatic')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'is_automatic']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
