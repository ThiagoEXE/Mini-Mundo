<x-app-layout>
    <form action="{{ route('projects.store') }}" method="POST">
    @csrf <div>
        <label for="name">Nome do Projeto (Obrigatório e Único)</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        @error('name') <span>{{ $message }}</span> @enderror
    </div>

    <div>
        <label for="description">Descrição (Opcional)</label>
        <textarea name="description" id="description">{{ old('description') }}</textarea>
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
        <input type="number" name="budget" id="budget" step="0.01" value="{{ old('budget') }}">
    </div>

    <button type="submit">Criar Projeto</button>
</form>
</x-app-layout>