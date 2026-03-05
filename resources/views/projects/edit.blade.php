<x-app-layout>
    <form action="{{ route('projects.update', $project->id) }}" method="POST">
    @csrf
    {{-- Oculta um input que avisa o Laravel que isso é um UPDATE --}}
    @method('PUT')
    <div>
        <label for="name">Nome do Projeto (Obrigatório e Único)</label>
        <input type="text" name="name" id="name" value="{{ $project->name }}" required>
        @error('name') <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="description">Descrição (Opcional)</label>
        <textarea name="description" id="description">{{ $project->description }}</textarea>
    </div>

    <div>
        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="Ativo" {{ old('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="Inativo" {{ old('status') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
        </select>
    </div>

    <div>
        <label for="budget">Orçamento Disponível (Opcional)</label>
        <input type="number" name="budget" id="budget" step="0.01" value="{{ $project->budget }}">
    </div>

    <button type="submit">Salvar Alterações</button>
</form>
</x-app-layout>