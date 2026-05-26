<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER IF NOT EXISTS books_zurnals_trigger
            AFTER UPDATE ON books
            FOR EACH ROW
            BEGIN
                INSERT INTO zurnals (gramata_id, operacija, lauks, veca_vertiba, jauna_vertiba, created_at, updated_at)
                VALUES (OLD.id, \'UPDATE\', \'nosaukums\', OLD.nosaukums, NEW.nosaukums, datetime(\'now\'), datetime(\'now\'));

                INSERT INTO zurnals (gramata_id, operacija, lauks, veca_vertiba, jauna_vertiba, created_at, updated_at)
                VALUES (OLD.id, \'UPDATE\', \'isbn\', OLD.isbn, NEW.isbn, datetime(\'now\'), datetime(\'now\'));

                INSERT INTO zurnals (gramata_id, operacija, lauks, veca_vertiba, jauna_vertiba, created_at, updated_at)
                VALUES (OLD.id, \'UPDATE\', \'pieejamie_eksemplari\', CAST(OLD.pieejamie_eksemplari AS TEXT), CAST(NEW.pieejamie_eksemplari AS TEXT), datetime(\'now\'), datetime(\'now\'));
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS books_zurnals_trigger');
    }
};
