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
        Schema::create('product_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->string('category'); // فئة الملف (صورة منتج، صورة معرض، فاتورة مشتريات، إلخ)
            $table->boolean('is_active')->default(true); // الحالة (نشط أو غير نشط)
            $table->integer('order')->default(0); // ترتيب العرض
            $table->timestamps();
            
            // المفاتيح الفريدة
            $table->unique(['product_id', 'file_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_files');
    }
};
