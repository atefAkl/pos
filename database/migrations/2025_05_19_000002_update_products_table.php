<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Rename active to is_active if it exists
            if (Schema::hasColumn('products', 'active')) {
                $table->renameColumn('active', 'is_active');
            }
            
            // Add new category columns
            if (!Schema::hasColumn('products', 'sub_category_id')) {
                $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
                $table->foreign('sub_category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('set null');
            }
            
            if (!Schema::hasColumn('products', 'parent_category_id')) {
                $table->unsignedBigInteger('parent_category_id')->nullable()->after('sub_category_id');
                $table->foreign('parent_category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('set null');
            }
            
            // Add index for better performance
            $table->index(['category_id', 'sub_category_id', 'parent_category_id']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('products', 'sub_category_id')) {
                $table->dropForeign(['sub_category_id']);
                $table->dropColumn('sub_category_id');
            }
            
            if (Schema::hasColumn('products', 'parent_category_id')) {
                $table->dropForeign(['parent_category_id']);
                $table->dropColumn('parent_category_id');
            }
            
            // Revert is_active to active if needed
            if (Schema::hasColumn('products', 'is_active') && !Schema::hasColumn('products', 'active')) {
                $table->renameColumn('is_active', 'active');
            }
        });
    }
}
