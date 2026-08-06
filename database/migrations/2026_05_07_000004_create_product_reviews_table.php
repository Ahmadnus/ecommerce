<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            // null = guest reviewer
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Guest name (used when user_id is null)
            $table->string('reviewer_name', 100)->nullable();

            // Guest email for spam prevention (not shown publicly)
            $table->string('reviewer_email', 255)->nullable();

            $table->unsignedTinyInteger('rating');   // 1–5

            $table->text('comment');

            // Moderation: pending | approved | rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            // Admin can pin helpful reviews to the top
            $table->boolean('is_pinned')->default(false);

            $table->timestamps();

            // One approved review per user per product
            $table->unique(['product_id', 'user_id'], 'unique_user_product_review');

            // Indexes for common queries
            $table->index(['product_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};