<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oportunidade;
use App\Models\OportunidadeMedicosInteressados;
use App\Models\Medico;

class OportunidadesController extends Controller
{
    public function listar(Request $request)
    {
        try{
            $userId = $request->query->get('userId');

            return \response()->json(Oportunidade::select('oportunidade.*', 
                    'oportunidade_cliente.nome as nomeCLiente', 'especialidade.nome as nomeEspecialidade')
                ->join('oportunidade_cliente','oportunidade.idoportunidade_cliente','oportunidade_cliente.id')
                ->join('especialidade','oportunidade.idespecialidade','especialidade.id')
                ->join('medico_oportunidade_cliente', 'oportunidade.idoportunidade_cliente', 'medico_oportunidade_cliente.idoportunidade_cliente')
                ->join('medico', 'medico_oportunidade_cliente.idmedico', 'medico.id')
                ->where('medico.user_id', '=', $userId)
                ->get());

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function candidatarse(Request $request)
    {
        try{
            $idOportunidade = $request['idOportunidade'];
            $userId = $request['userId'];
            
            $medico = Medico::where('user_id', '=', $userId)
                ->firstOrFail();

            $omi = new OportunidadeMedicosInteressados();
            $omi->idoportunidade = $idOportunidade;
            $omi->idmedico = $medico->id;
            $omi->data_hora_interesse = date("y-m-d H:i:s");
            $omi->idoportunidade_status = 2;
            $omi->save();

            return \response()->json(json_encode($omi));
        }catch(\Exception $ex){
            throw $ex;
        }
        
    }

    public function medicoInteressado(Request $request)
    {
        $userId = $request->query->get('userId');
        $opm = OportunidadeMedicosInteressados::select('oportunidade_medicos_interessados.idoportunidade as idoportunidade')
            ->where('oportunidade_medicos_interessados.idmedico', '=', $userId)
            ->get();

        return \response()->json($opm);
    }
}
