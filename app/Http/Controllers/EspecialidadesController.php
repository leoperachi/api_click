<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use App\Models\Especialidade;

class EspecialidadesController extends Controller
{
    public function index()
    {
        $tasks = [];
        return response()->json($tasks);
    }

    public function getEspecialidades(Request $request)
    {
        return \response()->json(Especialidade::select('especialidade.id', 'especialidade.nome')->get());
    }
}
