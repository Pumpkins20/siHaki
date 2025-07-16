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
        Schema::create('hki_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->enum('type', ['copyright', 'patent'])->default('copyright');
            $table->string('creation_type')->nullable(); // Sudah termasuk kolom yang dibutuhkan
            $table->text('description');
            $table->json('additional_data')->nullable(); // Sudah termasuk kolom yang dibutuhkan
            $table->integer('member_count')->default(2); // Sudah termasuk kolom yang dibutuhkan
            $table->enum('status', [
                'draft',
                'submitted', 
                'under_review',
                'revision_needed',
                'approved',
                'rejected'
            ])->default('draft');
            $table->timestamp('submission_date')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['reviewer_id', 'status']);
            $table->index('creation_type');
            $table->index('submission_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hki_submissions');

    }
};
