<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW reader_fines AS
            SELECT
                b.lasitajs_id,
                COUNT(*) AS kavejumu_skaits,
                COALESCE(SUM(
                    CURRENT_DATE - (b.aiznemsanas_datums + 14)
                ), 0) AS kavejuma_dienas,
                COALESCE(SUM(
                    (CURRENT_DATE - (b.aiznemsanas_datums + 14)) * 0.50
                ), 0) AS kopejais_sods
            FROM borrows b
            WHERE b.atdosanas_datums IS NULL
              AND (b.aiznemsanas_datums + 14) < CURRENT_DATE
            GROUP BY b.lasitajs_id
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS reader_fines');
    }
};
