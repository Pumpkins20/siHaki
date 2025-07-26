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
        Schema::table('hki_submissions', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('description');
            $table->string('kode_pos', 10)->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hki_submissions', function (Blueprint $table) {
            $table->dropColumn(['alamat', 'kode_pos']);
        });
    }
};
