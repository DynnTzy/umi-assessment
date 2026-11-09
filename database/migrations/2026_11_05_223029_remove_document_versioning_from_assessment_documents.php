<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_group_id');
            $table->dropColumn('version_number');
        });

        Schema::dropIfExists('assessment_document_groups');
    }

    public function down(): void
    {
        Schema::create('assessment_document_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('instrument_type');
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            $table->index([
                'assessment_item_id',
                'instrument_type',
            ]);
        });

        Schema::table('assessment_documents', function (Blueprint $table) {
            $table->foreignId('document_group_id')
                ->nullable()
                ->constrained('assessment_document_groups')
                ->nullOnDelete();

            $table->unsignedInteger('version_number')->default(1);
        });
    }
};
