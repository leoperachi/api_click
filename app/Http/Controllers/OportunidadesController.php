<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oportunidade;
use App\Models\OportunidadeMedicosInteressados;
use App\Models\Medico;
use Illuminate\Support\Facades\DB;

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
                ->where('oportunidade.idoportunidade_status', '=', 2)
                ->orWhere('oportunidade.idoportunidade_status', '=', 10)
                ->get());

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function candidatarse(Request $request)
    {
        
        try{
            DB::beginTransaction();
            $idOportunidade = $request['idOportunidade'];
            $userId = $request['userId'];
            
            $oportunidade = Oportunidade::where('id', '=', $idOportunidade)
                ->firstOrFail();
            
            $oportunidade->idoportunidade_status = 10;
            $oportunidade->save();

            $medico = Medico::where('user_id', '=', $userId)
                ->firstOrFail();

            $omi = new OportunidadeMedicosInteressados();
            $omi->idoportunidade = $idOportunidade;
            $omi->idmedico = $medico->id;
            $omi->data_hora_interesse = date("y-m-d H:i:s");
            $omi->idoportunidade_status = 2;
            $omi->save();
            DB::commit();

            return \response()->json(json_encode($omi));
        }catch(\Exception $ex){
            throw $ex;
        }
        
    }

    public function descandidatarse(Request $request)
    {
        try{
            DB::beginTransaction();
            $idOportunidade = $request['idOportunidade'];
            $userId = $request['userId'];
            
            $medico = Medico::where('user_id', '=', $userId)
                ->firstOrFail();
            
            OportunidadeMedicosInteressados::where('idmedico', '=', $medico->id)
                ->where('idoportunidade', '=', $idOportunidade)->delete();

            $oportunidadeMedicosInteressados = OportunidadeMedicosInteressados::
                where('idoportunidade', '=', $idOportunidade)->get();

            if(count($oportunidadeMedicosInteressados) == 0)
            {
                $oportunidade = Oportunidade::where('id', '=', $idOportunidade)
                    ->firstOrFail();

                $oportunidade->idoportunidade_status = 2;
                $oportunidade->save();
            }

            DB::commit();
            return \response()->json(json_encode(true));
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function medicoInteressado(Request $request)
    {
        $userId = $request->query->get('userId');

        $medico = Medico::where('user_id', '=', $userId)
            ->firstOrFail();

        $opm = OportunidadeMedicosInteressados::select('oportunidade_medicos_interessados.idoportunidade as idoportunidade')
            ->where('oportunidade_medicos_interessados.idmedico', '=', $medico->id)
            ->get();

        return \response()->json($opm);
    }
}
