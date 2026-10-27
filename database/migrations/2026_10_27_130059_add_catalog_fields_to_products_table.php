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
        Schema::table('products', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('price');
            $table->string('brand')->nullable()->after('category');
            $table->boolean('is_on_sale')->default(false)->after('brand');
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('is_on_sale');
            $table->string('company_profile')->nullable()->after('discount_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn(['category', 'brand', 'is_on_sale', 'discount_percent', 'company_profile']);
        });
    }
};
