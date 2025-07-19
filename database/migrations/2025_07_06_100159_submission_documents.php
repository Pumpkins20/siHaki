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
        Schema::create('submission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('hki_submissions')->onDelete('cascade');
            $table->enum('document_type', ['main_document', 'supporting_document', 'certificate']);
            $table->string('file_name');
            $table->string('file_path');
            $table->bigInteger('file_size'); // in bytes
            $table->string('mime_type')->nullable();
            $table->timestamp('uploaded_at');
            $table->timestamps();
            
            $table->index(['submission_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submission_documents');
    }
};
