<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('submission_members', function (Blueprint $table) {
            // Change ktp from string to text to store file path
            $table->text('ktp')->change();
        });
    }

    public function down()
    {
        Schema::table('submission_members', function (Blueprint $table) {
            $table->string('ktp', 16)->change();
        });
    }
};