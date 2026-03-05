<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validação (Super importante!)
        $validated = $request->validate([
            'name' => 'required|unique:projects|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Ativo,Inativo',
            'budget' => 'nullable|numeric|min:0',
        ]);

        // 2. Criação (Graças ao $fillable no Model)
        Project::create($validated);

        // 3. Redirecionamento
        return redirect()->route('projects.index')->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // 1. Validação (Garante que os dados são seguros e do tipo correto)
        $validated = $request->validate([
            'nome'        => 'required|string|max:255',
            'descricao'   => 'nullable|string',
            'status'      => 'required|in:planejamento,execucao,concluido',
            'data_entrega' => 'required|date',
        ]);

        // 2. Atualização (O preenchimento automático via array validado)
        $project->update($validated);

        // 3. Redirecionamento com Feedback
        return redirect()
            ->route('projects.index')
            ->with('status', 'Projeto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Usando o método que criamos no Model Project
        if ($project->hasTasks()) {
            return redirect()->back()->with('error', 'Não é possível excluir um projeto com tarefas ativas.');
        }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projeto removido com sucesso.');
    }
}
