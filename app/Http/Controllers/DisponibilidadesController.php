<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicoDisponibilidade;
use App\User;
use App\Models\MedicoEspecialidade;
use App\Models\Medico;

class DisponibilidadesController extends Controller
{
    public function index()
    {
        
    }

    public function minhasDisponiblidades(Request $request)
    {
        $userId = $request->query->get('usr');

        try{
            $medico = Medico::where('user_id', '=', $userId)
            ->firstOrFail();

            $md = MedicoDisponibilidade::select('medico_disponibilidade.*', 'especialidade.nome')
                ->join('medico_especialidade', 'medico_disponibilidade.idmedico_especialidade', 'medico_especialidade.id')
                ->join('especialidade', 'medico_especialidade.idespecialidade', 'especialidade.id')
                ->join('medico', 'medico_especialidade.idmedico', 'medico.id')
                ->where('medico.id', '=', $medico->id)
                ->get();

            return \response()->json($md);
        }catch(\Exception $ex){
            $ret = [
                'sucesso' => false,
                'msg' => 'Usuário não é tem Medico Associado'
            ];

            return $ret;
        }
    }

    public function salvar(Request $request)
    {
        $r = $request['form_params'];

        $u = User::where('email', $request['nomeMedico'])->first();
        $m = Medico::where('user_id', $u->id)->first();
        $mes = MedicoEspecialidade::where('idmedico', $m->id)->get();

        $md = new MedicoDisponibilidade();

        if(count($mes) > 0){
            $md->idmedico_especialidade = $mes[0]->id;
        }
        else{
            $me = new MedicoEspecialidade();
            $me->idmedico = $m->id;
            $me->idespecialidade = $request['idmedico_especialidade'];
            $me->ativo = 1;
            $me->save();
            $md->idmedico_especialidade = $me->id;
        }
       
        $md->data_inicio_especifica = date('Y-m-d', strtotime($request['data_inicio_especifica']));
        $md->data_termino_especifica =  date('Y-m-d', strtotime($request['data_termino_especifica']));
        $md->hora_inicio =  $request['hora_inicio'];
        $md->hora_termino = $request['hora_termino'];
        $md->idoportunidade_tipo = $request['idoportunidade_tipo'];
        if(isset($request['diaSemana'])) 
        {
            if($request['diaSemana'] == 1){
                $md->segunda = 1;
            }
            else if($request['diaSemana'] == 2){
                $md->terca = 1;
            }
            else if($request['diaSemana'] == 3){
                $md->quarta = 1;
            }
            else if($request['diaSemana'] == 4){
                $md->quinta = 1;
            }
            else if($request['diaSemana'] == 5){
                $md->sexta = 1;
            }
            else if($request['diaSemana'] == 6){
                $md->sabado = 1;
            }
            else if($request['diaSemana'] == 7){
                $md->domingo = 1;
            }
            else if($r['diaSemana'] == 8){
                $md->combinar = 1;
            }
        }

        $md->ativo = 1;
        $md->data_criacao = date('Y-m-d', strtotime($request['data_inicio_especifica']));
        $md->data_atualizacao = date('Y-m-d', strtotime($request['data_inicio_especifica']));

        try{
            $md->save();

            return \response()->json(json_encode($md));

        }catch(\Exception $ex){

            return \response()->json([ 
                        'sucesso' => false, 
                        'msg' => $ex->getmessage()
                    ]);
        }
    }
}
