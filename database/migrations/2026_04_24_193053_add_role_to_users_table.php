<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // buat migration baru: php artisan make:migration add_role_to_users_table
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role', 50)->default('kasir')->after('email');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
};
