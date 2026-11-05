<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
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
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_document_groups');
    }
};
