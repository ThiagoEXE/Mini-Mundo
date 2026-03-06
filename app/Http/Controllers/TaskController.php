<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Project $project)
    {
        return view('tasks.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'description'         => 'required|string|max:500',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'predecessor_task_id' => 'nullable|exists:tasks,id',
            'status'              => 'required|in:Concluída,Não Concluída',
        ]);

        // Cria a tarefa vinculada ao projeto
        $project->tasks()->create($validated);

        // Retorna para a mesma página (show) com mensagem de sucesso
        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Tarefa adicionada!');
    }

    public function edit(Project $project, Task $task)
    {
        // Aqui você recebe os dois objetos automaticamente
        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $task->update($request->all());

        return redirect()->route('projects.show', $project->id);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, Task $task)
    {
        // Usando o método que criamos no Model Task
        if ($task->hasDependents()) {
            return redirect()->back()->with('error', 'Esta tarefa é pré-requisito para outra e não pode ser excluída.');
        }

        $task->delete();
        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Tarefa excluída com sucesso!');
    }
}
