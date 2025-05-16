<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('name', 125)->change();
            $table->string('guard_name', 125)->change();
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->string('name', 125)->change();
            $table->string('guard_name', 125)->change();
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('guard_name')->change();
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('guard_name')->change();
        });
    }
};
