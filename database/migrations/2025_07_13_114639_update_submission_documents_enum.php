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
        DB::statement("ALTER TABLE submission_documents MODIFY COLUMN document_type ENUM(
            'main_document', 
            'supporting_document', 
            'certificate',
            'manual_document',
            'video_file',
            'ebook_file', 
            'image_file',
            'tool_photo',
            'metadata_file',
            'additional_photo'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE submission_documents MODIFY COLUMN document_type ENUM(
            'main_document', 
            'supporting_document', 
            'certificate'
        ) NOT NULL");
    }
};
