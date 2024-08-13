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
    
        if ($request->has('priority')) {
            $tasks = $tasks->where('priority', '=', $request->input('priority'));
        }
        if ($request->has('upcoming')) {
            $tasks = $tasks->where('before_date', '>=', date('Y-m-d H-i'));
        }
        if ($request->has('onlyDone')) {
            $tasks = $tasks->where('is_done', '=', true);
        }
    
        // Fetch tasks before sorting and grouping
        $tasks = $tasks->get();
    
        if ($request->has('sort')) {
            $sortField = $request->input('sort');
            $sortOrder = $request->input('order', 'asc'); // Default to ascending if not specified
    
            if ($sortField == 'priority') {
                $tasks = $tasks->sortBy(function ($task) {
                    return array_search($task->priority, ['low', 'mid', 'high']);
                }, SORT_REGULAR, $sortOrder == 'desc');
            } else {
                $tasks = $tasks->sortBy($sortField, SORT_REGULAR, $sortOrder == 'desc');
            }
        }
    
        if ($request->has('groupBy')) {
            $groupBy = $request->input('groupBy');
            $tasks = $tasks->groupBy($groupBy);
        }
    
        return response()->json([
            'data' => $tasks
        ]);
    }
    public function show( $id){
        $task = Task::findOrFail($id);
        return response()->json(['data'=>$task]);
    }
    public function update(Request $request, $id){
    $inputs = $request->validate([
        'name' => ['string', 'sometimes'],
            'description' => ['max:1000', 'sometimes'],
            'is_done' => ['boolean','sometimes'],
            'before_date' => ['sometimes'],
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
    public function complete($id){
        $task = Task::where('id', "=",$id)->where("is_done","=",true)->firstOrFail();
        $task->update(['is_done'=> true]);
        return response()->json(['data'=> 'the task is changed to done'] );
    }
    public function cancel($id){
        $task = Task::where('id', "=",$id)->where("is_done","=",false)->firstOrFail();
        $task->update(['is_done'=> false]);
        return response()->json(['data'=> 'the task is changed to undone'] );
    }
}