<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_documents', function (Blueprint $table) {
            $table->string('upload_action')
                ->default('initial')
                ->after('version_number');

            $table->text('upload_note')->nullable()->after('upload_action');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_documents', function (Blueprint $table) {
            $table->dropColumn([
                'upload_action',
                'upload_note',
            ]);
        });
    }
};
