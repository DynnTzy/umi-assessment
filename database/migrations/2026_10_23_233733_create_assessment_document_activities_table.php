<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_document_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_document_id')
                ->constrained('assessment_documents')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // initial_upload, reupload, revision, review
            $table->string('type');

            // Informasi file yang diunggah saat aktivitas terjadi
            $table->string('file_name')->nullable();

            // Status penilaian asesor, jika aktivitas berupa review
            $table->string('status')->nullable();

            // Catatan upload, revisi, atau penilaian asesor
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'assessment_document_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_document_activities');
    }
};
