<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add new columns only if they don't exist
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained();
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        // Modify role column using raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'reviewer', 'admin', 'pengguna') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['department_id']);
            
            // Drop columns that we added
            $table->dropColumn(['phone', 'department_id', 'is_active']);
        });
        
        // Revert role column to original values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pengguna') DEFAULT 'pengguna'");
    }
};
