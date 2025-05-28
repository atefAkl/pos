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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('name'); // اسم الملف الأصلي
            $table->string('display_name')->nullable(); // اسم العرض المقترح
            $table->string('mime_type'); // نوع الملف
            $table->string('extension'); // امتداد الملف
            $table->unsignedBigInteger('size'); // حجم الملف بالبايت
            $table->string('alt_text')->nullable(); // النص البديل
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
