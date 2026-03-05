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
            <input type="text"
                name="budget"
                id="budget"
                value="{{ old('budget', number_format($project->budget, 2, ',', '.')) }}"
                >
        </div>

        <button type="submit">Salvar Alterações</button>
        <a href="{{ route('projects.index') }}" class="ml-2">Cancelar</a>
    </form>
    <script>
        const input = document.getElementById('budget');
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            e.target.value = value;
        });
    </script>
</x-app-layout>