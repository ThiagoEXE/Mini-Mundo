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

        $validated = $request->validate(
            [
                'description'         => 'required|unique:tasks,description|string|max:500',
                // Se 'end_date' for enviado, 'start_date' torna-se obrigatório
                'start_date'          => 'nullable|required_with:end_date|date',
                // 'end_date' só é validado se 'start_date' também estiver presente
                'end_date'            => 'nullable|date|after_or_equal:start_date',
                'predecessor_task_id' => 'nullable|exists:tasks,id',
                'status'              => 'required|in:Concluída,Não Concluída',
            ],
            [
                'description.unique' => 'Já existe uma tarefa com esta descrição!',
                'start_date.required_with' => 'Para definir uma data de término, você precisa informar uma data de início.',
                'end_date.after_or_equal' => 'A data de término não pode ser anterior à data de início.'
            ],
        );

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
        $request->validate([
            // Se 'end_date' for enviado, 'start_date' torna-se obrigatório
            'start_date'          => 'nullable|required_with:end_date|date',
            // 'end_date' só é validado se 'start_date' também estiver presente
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'description' => "required|unique:tasks,description,{$task->id}",
        ], [
            'description.required' => 'A descrição é obrigatória.',
            'description.unique' => 'Esta descrição já existe em outra tarefa.',
            'start_date.required_with' => 'Para definir uma data de término, você precisa informar uma data de início.',
            'end_date.after_or_equal' => 'A data de término não pode ser anterior à data de início.'
        ]);

        // Se a validação passar, você atualiza:
        $task->update($request->all());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Tarefa atualizada!');
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
