<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                // TODO 1: Tambahkan kolom 'nilai' (unsignedTinyInteger, nullable) dan 'catatan_dosen' (text, nullable) setelah kolom 'jawaban'
                if (!Schema::hasColumn('submissions', 'nilai')) {
                    $table->unsignedTinyInteger('nilai')->nullable()->after('jawaban');
                }
                if (!Schema::hasColumn('submissions', 'catatan_dosen')) {
                    $table->text('catatan_dosen')->nullable()->after('nilai');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                // TODO 1 (Rollback): Hapus kolom 'nilai' dan 'catatan_dosen' menggunakan $table->dropColumn(...)
                $columns = array_filter(['nilai', 'catatan_dosen'], fn($col) => Schema::hasColumn('submissions', $col));
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};