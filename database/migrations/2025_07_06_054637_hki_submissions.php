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
            $table->string('creation_type')->nullable()->after('type');
            $table->json('additional_data')->nullable()->after('description');
            $table->integer('member_count')->default(2)->after('additional_data');
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
            $table->dropColumn(['creation_type', 'additional_data', 'member_count']);
        });
    }
};
