<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oportunidade;

class OportunidadesController extends Controller
{
    public function listar(Request $request)
    {
        return \response()->json(Oportunidade::select('oportunidade.*', 
                'oportunidade_cliente.nome as nomeCLiente', 'especialidade.nome as nomeEspecialidade')
            ->join('oportunidade_cliente','oportunidade.idoportunidade_cliente','oportunidade_cliente.id')
            ->join('especialidade','oportunidade.idespecialidade','especialidade.id')
            ->get());
    }

    public function candidatarse(Request $request)
    {
        $r = $request;
        
    }
}
