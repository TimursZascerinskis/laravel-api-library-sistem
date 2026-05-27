<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    public function calculate(Reader $reader)
    {
        $result = DB::select('
            SELECT
                COUNT(*) AS kavejumu_skaits,
                COALESCE(SUM(
                    CAST(julianday(DATE(\'now\')) - julianday(DATE(b.aiznemsanas_datums, \'+14 days\')) AS INTEGER)
                ), 0) AS kavejuma_dienas,
                COALESCE(SUM(
                    CAST(julianday(DATE(\'now\')) - julianday(DATE(b.aiznemsanas_datums, \'+14 days\')) AS INTEGER) * 0.50
                ), 0) AS kopejais_sods
            FROM borrows b
            WHERE b.lasitajs_id = ?
              AND b.atdosanas_datums IS NULL
              AND DATE(b.aiznemsanas_datums, \'+14 days\') < DATE(\'now\')
        ', [$reader->id]);

        $data = [
            'lasitajs_id' => $reader->id,
            'lasitaja_vards' => $reader->vards,
            'lasitaja_e_pasts' => $reader->e_pasts,
            'kavejumu_skaits' => $result[0]->kavejumu_skaits,
            'kavejuma_dienas' => (int) $result[0]->kavejuma_dienas,
            'sods_par_dienu' => 0.50,
            'kopejais_sods' => (float) $result[0]->kopejais_sods,
        ];

        if (request()->expectsJson()) {
            return response()->json($data);
        }

        return view('reader-fine', ['data' => $data]);
    }
}
