<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE FUNCTION books_zurnals_trigger_func()
            RETURNS TRIGGER AS \$\$
            BEGIN
                INSERT INTO zurnals (gramata_id, operacija, lauks, veca_vertiba, jauna_vertiba, created_at, updated_at)
                VALUES (OLD.id, 'UPDATE', 'nosaukums', OLD.nosaukums, NEW.nosaukums, NOW(), NOW());

                INSERT INTO zurnals (gramata_id, operacija, lauks, veca_vertiba, jauna_vertiba, created_at, updated_at)
                VALUES (OLD.id, 'UPDATE', 'isbn', OLD.isbn, NEW.isbn, NOW(), NOW());

                INSERT INTO zurnals (gramata_id, operacija, lauks, veca_vertiba, jauna_vertiba, created_at, updated_at)
                VALUES (OLD.id, 'UPDATE', 'pieejamie_eksemplari', CAST(OLD.pieejamie_eksemplari AS TEXT), CAST(NEW.pieejamie_eksemplari AS TEXT), NOW(), NOW());

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared('DROP TRIGGER IF EXISTS books_zurnals_trigger ON books');
        DB::unprepared('
            CREATE TRIGGER books_zurnals_trigger
            AFTER UPDATE ON books
            FOR EACH ROW
            EXECUTE FUNCTION books_zurnals_trigger_func()
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS books_zurnals_trigger ON books');
        DB::unprepared('DROP FUNCTION IF EXISTS books_zurnals_trigger_func');
    }
};
