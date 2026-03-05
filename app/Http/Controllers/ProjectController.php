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
        // 1. Limpeza Radical: Remove tudo que não é número ou vírgula, 
        // depois troca a vírgula por ponto.
        if ($request->filled('budget')) {
            // Remove pontos de milhar e outros caracteres, mantendo apenas a vírgula decimal
            $rawBudget = $request->budget;
            $cleanBudget = str_replace('.', '', $rawBudget); // Remove o ponto de milhar: 15.000,00 -> 15000,00
            $cleanBudget = str_replace(',', '.', $cleanBudget); // Troca vírgula por ponto: 15000,00 -> 15000.00

            // Injeta de volta na request para a validação aceitar como 'numeric'
            $request->merge(['budget' => $cleanBudget]);
        }

        // 2. Validação
        $validated = $request->validate([
            'name'        => 'required|unique:projects|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:Ativo,Inativo',
            'budget'      => 'nullable|numeric|min:0', // Agora 'numeric' vai entender o ponto decimal
        ]);

        // 3. Criação
        Project::create($validated);

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
        // 1. Mesma limpeza que fizemos no store
        if ($request->filled('budget')) {
            $cleanBudget = str_replace('.', '', $request->budget); // Remove milhar
            $cleanBudget = str_replace(',', '.', $cleanBudget);    // Troca decimal
            $request->merge(['budget' => $cleanBudget]);
        }

        // 2. Validação (ignora o ID atual na verificação de 'unique')
        $validated = $request->validate([
            'name'        => 'required|max:255|unique:projects,name,' . $project->id,
            'description' => 'nullable|string',
            'status'      => 'required|in:Ativo,Inativo',
            'budget'      => 'nullable|numeric|min:0',
        ]);

        // 3. Atualização
        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Projeto atualizado com sucesso!');
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
