<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submission_members', function (Blueprint $table) {
            $table->text('alamat')->after('email'); // ✅ NEW: alamat column
            $table->string('kode_pos', 5)->after('alamat'); // ✅ NEW: kode_pos column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submission_members', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'kode_pos']);
        });
    }
};