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
            $table->string('certificate_path')->nullable()->after('review_notes');
            $table->timestamp('certificate_issued_at')->nullable()->after('certificate_path');
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
            $table->dropColumn(['certificate_path', 'certificate_issued_at']);
        });
    }
};
