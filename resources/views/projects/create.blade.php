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
            <input type="text"
                name="budget"
                id="budget"
                class="form-control"
                placeholder="0,00"
                value="{{ old('budget') }}"
                onkeyup="formatCurrency(this)">
        </div>

        <button type="submit">Criar Projeto</button>
    </form>
    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, ""); // Remove tudo que não é número
            value = (value / 100).toFixed(2) + "";
            value = value.replace(".", ","); // Substitui ponto por vírgula
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,"); // Adiciona ponto de milhar
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,"); // Adiciona ponto de milhar
            input.value = value;
        }
    </script>
</x-app-layout>