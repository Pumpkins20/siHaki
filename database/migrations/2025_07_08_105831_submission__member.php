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
        Schema::create('submission_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('hki_submissions')->onDelete('cascade');
            $table->string('name');
            $table->string('whatsapp', 15);
            $table->string('email');
            $table->string('ktp', 16);
            $table->integer('position'); // 1, 2, 3, dst
            $table->boolean('is_leader')->default(false);
            $table->timestamps();
            
            $table->index(['submission_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submission_members');
    }
};
