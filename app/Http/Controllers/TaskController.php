<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
                'description' => [
                    'required',
                    'string',
                    'max:500',
                    // A regra unique DEVE estar aqui dentro do array da description
                    Rule::unique('tasks')->where(function ($query) use ($request, $project) {
                        return $query->where('project_id', $project->id)
                            ->where('description', $request->description)
                            ->where('status', $request->status)
                            // Tratamento especial para datas que podem ser nulas
                            ->where(function ($q) use ($request) {
                                $request->start_date
                                    ? $q->where('start_date', $request->start_date)
                                    : $q->whereNull('start_date');
                            })
                            ->where(function ($q) use ($request) {
                                $request->end_date
                                    ? $q->where('end_date', $request->end_date)
                                    : $q->whereNull('end_date');
                            });
                    }),
                ],
                // Se 'end_date' for enviado, 'start_date' torna-se obrigatório
                'start_date'          => 'nullable|required_with:end_date|date',
                // 'end_date' só é validado se 'start_date' também estiver presente
                'end_date'            => 'nullable|date|after_or_equal:start_date',
                'predecessor_task_id' => 'nullable|exists:tasks,id',
                'status'              => 'required|in:Concluída,Não Concluída',
            ],
            [
                'description.unique' => 'Já existe uma tarefa cadastrada com estes mesmos detalhes (descrição, datas e status) neste projeto.',
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
        $validated = $request->validate([
            'description' => [
                'required',
                'string',
                'max:500',
                Rule::unique('tasks')->where(function ($query) use ($request, $project) {
                    return $query->where('project_id', $project->id)
                        ->where('description', $request->description)
                        ->where('status', $request->status)
                        ->where(function ($q) use ($request) {
                            $request->start_date
                                ? $q->where('start_date', $request->start_date)
                                : $q->whereNull('start_date');
                        })
                        ->where(function ($q) use ($request) {
                            $request->end_date
                                ? $q->where('end_date', $request->end_date)
                                : $q->whereNull('end_date');
                        });
                })->ignore($task->id), // ESSENCIAL: Ignora a própria tarefa na verificação
            ],
            'start_date'          => 'nullable|required_with:end_date|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'status'              => 'required|in:Concluída,Não Concluída',
            'predecessor_task_id' => 'nullable|exists:tasks,id',
        ], [
            'description.unique'       => 'Já existe outra tarefa com esses mesmos detalhes neste projeto.',
            'start_date.required_with' => 'Para definir uma data de término, você precisa informar uma data de início.',
            'end_date.after_or_equal'  => 'A data de término não pode ser anterior à data de início.'
        ]);

        // Use $validated em vez de $request->all() por segurança
        $task->update($validated);

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
