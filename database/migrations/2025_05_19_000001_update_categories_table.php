<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Rename 'active' to 'is_active' if it exists
            if (Schema::hasColumn('categories', 'active')) {
                $table->renameColumn('active', 'is_active');
            }
            
            // Add new columns
            if (!Schema::hasColumn('categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('categories', 'level')) {
                $table->tinyInteger('level')->default(1)->after('parent_id');
            }
            
            // Add index for better performance
            $table->index(['parent_id', 'level', 'is_active']);
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop foreign key first
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            if (Schema::hasColumn('categories', 'level')) {
                $table->dropColumn('level');
            }
            
            // Revert 'is_active' to 'active' if needed
            if (Schema::hasColumn('categories', 'is_active') && !Schema::hasColumn('categories', 'active')) {
                $table->renameColumn('is_active', 'active');
            }
        });
    }
}
