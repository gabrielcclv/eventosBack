<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prediccion;
use App\Models\Partido;
use Carbon\Carbon;

class PrediccionController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'partido_id' => 'required|exists:partidos,id',
            'goles_local_predicho' => 'required|integer',
            'goles_visitante_predicho' => 'required|integer',
        ]);

        $partido = Partido::findOrFail($request->partido_id);

        $hoy = Carbon::now()->startOfDay();
        $diaDelPartido = Carbon::parse($partido->fecha_partido)->startOfDay();

        if ($hoy->gte($diaDelPartido)) {
            return response()->json([
                'success' => false, 
                'error' => 'El plazo para predecir o modificar este partido ha cerrado.'
            ], 403);
        }

        $prediccion = Prediccion::updateOrCreate(
            ['user_id' => $request->user()->id, 'partido_id' => $partido->id],
            [
                'goles_local_predicho' => $request->goles_local_predicho,
                'goles_visitante_predicho' => $request->goles_visitante_predicho
            ]
        );

        return response()->json(['success' => true, 'prediccion' => $prediccion], 200);
    }
}