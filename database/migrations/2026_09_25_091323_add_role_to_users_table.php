
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom role sudah dibuat di create_users_table.
        // Tidak perlu menambahkan kolom lagi.
    }

    public function down(): void
    {
        // Jangan hapus role karena dimiliki migration create_users_table.
    }
};
