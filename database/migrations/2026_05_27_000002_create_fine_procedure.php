<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE VIEW IF NOT EXISTS reader_fines AS
            SELECT
                b.lasitajs_id,
                COUNT(*) AS kavejumu_skaits,
                COALESCE(SUM(
                    CAST(julianday(DATE(\'now\')) - julianday(DATE(b.aiznemsanas_datums, \'+14 days\')) AS INTEGER)
                ), 0) AS kavejuma_dienas,
                COALESCE(SUM(
                    CAST(julianday(DATE(\'now\')) - julianday(DATE(b.aiznemsanas_datums, \'+14 days\')) AS INTEGER) * 0.50
                ), 0) AS kopejais_sods
            FROM borrows b
            WHERE b.atdosanas_datums IS NULL
              AND DATE(b.aiznemsanas_datums, \'+14 days\') < DATE(\'now\')
            GROUP BY b.lasitajs_id
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS reader_fines');
    }
};
