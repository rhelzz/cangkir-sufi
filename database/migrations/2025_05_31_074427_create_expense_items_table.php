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
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        // Remove amount field from expenses table as we'll calculate it from items
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back amount field
        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable();
        });
        
        Schema::dropIfExists('expense_items');
    }
};
