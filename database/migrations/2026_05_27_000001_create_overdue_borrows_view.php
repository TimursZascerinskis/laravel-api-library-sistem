<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW overdue_borrows AS
            SELECT
                b.id AS borrow_id,
                bo.nosaukums AS gramatas_nosaukums,
                bo.isbn,
                r.vards AS lasitaja_vards,
                r.e_pasts AS lasitaja_e_pasts,
                b.aiznemsanas_datums,
                (b.aiznemsanas_datums + 14) AS planotais_atdosanas_datums,
                (CURRENT_DATE - b.aiznemsanas_datums - 14) AS kavejuma_dienas
            FROM borrows b
            JOIN books bo ON b.gramata_id = bo.id
            JOIN readers r ON b.lasitajs_id = r.id
            WHERE b.atdosanas_datums IS NULL
              AND (b.aiznemsanas_datums + 14) < CURRENT_DATE
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS overdue_borrows');
    }
};
