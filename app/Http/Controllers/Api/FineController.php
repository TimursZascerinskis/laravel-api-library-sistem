<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reader;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    public function index()
    {
        $fines = DB::table('reader_fines')
            ->join('readers', 'reader_fines.lasitajs_id', '=', 'readers.id')
            ->select('reader_fines.*', 'readers.vards', 'readers.e_pasts')
            ->orderBy('kopejais_sods', 'desc')
            ->get();

        if (request()->expectsJson()) {
            return response()->json($fines->map(fn ($fine) => $this->addLinks($fine)));
        }

        return view('fines-index', ['fines' => $fines]);
    }

    public function calculate(Reader $reader)
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            $result = DB::select('
                SELECT
                    COUNT(*) AS kavejumu_skaits,
                    COALESCE(SUM(
                        CURRENT_DATE - (b.aiznemsanas_datums + 14)
                    ), 0) AS kavejuma_dienas,
                    COALESCE(SUM(
                        (CURRENT_DATE - (b.aiznemsanas_datums + 14)) * 0.50
                    ), 0) AS kopejais_sods
                FROM borrows b
                WHERE b.lasitajs_id = ?
                  AND b.atdosanas_datums IS NULL
                  AND (b.aiznemsanas_datums + 14) < CURRENT_DATE
            ', [$reader->id]);
        } else {
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
        }

        $data = [
            'lasitajs_id' => $reader->id,
            'lasitaja_vards' => $reader->vards,
            'lasitaja_e_pasts' => $reader->e_pasts,
            'kavejumu_skaits' => $result[0]->kavejumu_skaits,
            'kavejuma_dienas' => (int) $result[0]->kavejuma_dienas,
            'sods_par_dienu' => 0.50,
            'kopejais_sods' => (float) $result[0]->kopejais_sods,
        ];

        $data['_links'] = [
            'reader' => ['href' => route('readers.show', $reader->id)],
        ];

        if (request()->expectsJson()) {
            return response()->json($data);
        }

        return view('reader-fine', ['data' => $data]);
    }

    private function addLinks($fine)
    {
        $data = (array) $fine;
        $data['_links'] = [
            'reader'       => ['href' => route('readers.show', $fine->lasitajs_id)],
            'reader_fines' => ['href' => route('fines.calculate', $fine->lasitajs_id)],
        ];

        return (object) $data;
    }
}
