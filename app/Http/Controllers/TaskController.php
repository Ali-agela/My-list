<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{


    public function store(Request $request)
    {
        $inputs = $request->validate([
            'name' => ['string', 'required'],
            'description' => ['max:1000'],
            'is_done' => ['boolean'],
            'before_date' => [],
            'priority' => ['required', 'string', 'in:low,mid,high']
        ]);

        Task::create($inputs);

        return response()->json([
            'data' => 'task created successfully'
        ]);
    }

    public function index(Request $request)
    {
        $tasks = Task::query();

        if ($request->has('priority')){
            $tasks = $tasks->where('priority','=',$request->input('priority'));
        }
        if ($request->has('upcoming')){
            $tasks = $tasks->where('before_date','>=',date('Y-m-d H-i'));
        }
        if ($request->has('sort')){
            $tasks = $tasks->OrderBy($request->input('sort'));
        }
        if($request->has('onlyDone')){
            $tasks = $tasks->where("is_done","=",true);
        }
        return response()->json([
            'data' => $tasks->get()
        ]);
    }
    public function show( $id){
        $task = Task::findOrFail($id);
        return response()->json(['data'=>$task]);
    }
    public function update(Request $request, $id){
    $inputs = $request->validate([
        'name' => ['string', 'required'],
            'description' => ['max:1000'],
            'is_done' => ['boolean'],
            'before_date' => [],
            'priority' => ['required', 'string', 'in:low,mid,high']
        ]);
        Task::findOrFail($id)->update($inputs);
        return response()->json(['data'=>'updated task'] );
    }
    public function destroy($id){
        $task = Task::findOrFail($id);
        $task->delete();
        return response()->json(['data'=>'task deleted']) ;
    }
}
