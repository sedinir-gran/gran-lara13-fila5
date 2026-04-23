<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add is_admin and avatar_url columns to users table
     *
     * @tested-by \Tests\Feature\Database\UsersTableTest
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->string('avatar_url')->nullable();
        });
    }

    /**
     * Remove is_admin and avatar_url columns from users table
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
            $table->dropColumn('avatar_url');
        });
    }
};
