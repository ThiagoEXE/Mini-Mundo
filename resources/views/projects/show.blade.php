<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gerenciar Projetos') }}
        </h2>
    </x-slot>

    <div class="py-6"> <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form id="project-form" action="{{ route('projects.store') }}" method="POST" class="flex flex-wrap md:flex-nowrap items-center gap-3">
                    @csrf
                    <div id="method-field"></div>

                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="name" id="input-name" placeholder="Nome do Projeto" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm" required>
                    </div>

                    <div class="flex-[1.5] min-w-[250px]">
                        <input type="text" name="description" id="input-description" placeholder="Descrição (Opcional)" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                    </div>

                    <div class="w-32">
                        <input type="text" name="budget" id="input-budget" placeholder="0,00" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm" onkeyup="formatCurrency(this)">
                    </div>

                    <div class="w-32">
                        <select name="status" id="input-status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" id="submit-button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap">
                            Salvar
                        </button>
                        <button type="button" id="cancel-button" onclick="resetForm()" class="hidden bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded text-sm whitespace-nowrap">
                            X
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Projeto</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Descrição</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Orçamento</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-4 py-3 text-center text-sm dark:text-gray-200 font-medium">{{ $project->name }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($project->description, 40) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $project->status == 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm dark:text-gray-200">R$ {{ number_format($project->budget, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center text-sm space-x-2">
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-blue-500 hover:underline">Tarefas</a>
                                    <button onclick="editProject({{ $project }})" class="text-indigo-500 hover:underline">Editar</button>
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Excluir?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">Nenhum projeto encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function formatCurrency(input) {
            let value = input.value.replace(/\D/g, "");
            value = (value / 100).toFixed(2) + "";
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            input.value = value;
        }

        function editProject(project) {
            const form = document.getElementById('project-form');
            const methodField = document.getElementById('method-field');
            const cancelBtn = document.getElementById('cancel-button');

            form.action = `/projects/${project.id}`;
            methodField.innerHTML = `@method('PUT')`;
            cancelBtn.classList.remove('hidden');

            document.getElementById('input-name').value = project.name;
            document.getElementById('input-description').value = project.description || '';
            document.getElementById('input-status').value = project.status;
            
            let budgetInput = document.getElementById('input-budget');
            budgetInput.value = (project.budget).toLocaleString('pt-BR', { minimumFractionDigits: 2 });
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.getElementById('input-name').focus();
        }

        function resetForm() {
            const form = document.getElementById('project-form');
            form.action = "{{ route('projects.store') }}";
            document.getElementById('method-field').innerHTML = '';
            document.getElementById('cancel-button').classList.add('hidden');
            form.reset();
        }
    </script>
</x-app-layout>