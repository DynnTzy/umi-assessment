
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessment_documents', function (Blueprint $table) {
            $table->string('instrument_type')
                ->default('dokumen')
                ->after('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::table('assessment_documents', function (Blueprint $table) {
            $table->dropColumn('instrument_type');
        });
    }
};
