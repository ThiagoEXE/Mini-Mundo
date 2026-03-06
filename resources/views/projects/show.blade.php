<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Projeto: {{ $project->name }}
            </h2>
            <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">
                &larr; Voltar para a lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Detalhes do Projeto</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $project->description }}</p>
                <div class="mt-4 flex gap-4 text-sm">
                    <span><strong>Status:</strong> {{ $project->status }}</span>
                    <span><strong>Orçamento:</strong> R$ {{ number_format($project->budget, 2, ',', '.') }}</span>
                </div>
            </div>
            @php
                $total = $project->tasks->count();
                $concluidas = $project->tasks->where('status', 'Concluída')->count();
                $percentual = $total > 0 ? ($concluidas / $total) * 100 : 0;
            @endphp

            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-6">
                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentual }}%"></div>
                <span class="text-xs text-gray-500">{{ round($percentual) }}% concluído</span>
            </div>
            <div class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tarefas Associadas</h3>
                    <a href="{{ route('projects.tasks.create', $project->id) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        + Nova Tarefa
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição da
                                Tarefa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prazos
                                (Início/Fim)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Predecessora
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr class="bg-blue-50 dark:bg-gray-900/40">
                            <form action="{{ route('projects.tasks.store', $project->id) }}" method="POST">
                                @csrf
                                <td class="px-4 py-4">
                                    <input type="text" name="description" placeholder="Descrição" required
                                        class="block w-full rounded-md border-gray-300 text-xs dark:bg-gray-800">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-1">
                                        <input type="date" name="start_date"
                                            class="text-xs rounded-md border-gray-300 dark:bg-gray-800">
                                        <input type="date" name="end_date"
                                            class="text-xs rounded-md border-gray-300 dark:bg-gray-800">
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <select name="predecessor_task_id" class="w-full text-xs rounded-md ...">
                                        <option value="">-- Sem Predecessora --</option>
                                        @foreach ($project->tasks as $pTask)
                                            <option value="{{ $pTask->id }}">{{ $pTask->description }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <select name="status"
                                        class="w-full text-xs rounded-md border-gray-300 dark:bg-gray-800">
                                        <option value="Não Concluída">Não Concluída</option>
                                        <option value="Concluída">Concluída</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-3 py-2 rounded text-xs font-bold uppercase hover:bg-blue-700">
                                        Add
                                    </button>

                                </td>
                            </form>
                        </tr>
                        @foreach ($project->tasks as $task)
                            <tr x-data="{ editando: false }" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">

                                <!-- Exibição -->
                                <template x-if="!editando">
                                    <td class="px-4 py-4" colspan="4">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ $task->description }}</div>
                                                <div class="text-xs text-gray-500">{{ $task->description }}</div>
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d/m') : '-' }}
                                                a
                                                {{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d/m') : '-' }}
                                            </div>
                                            @if ($task->predecessor)
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Depende
                                                        de:</span>
                                                    <span
                                                        class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                                        {{ $task->predecessor->description }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] {{ $task->predecessor->status == 'Concluída' ? 'text-green-500' : 'text-amber-500' }}">
                                                        ({{ $task->predecessor->status }})
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-300 text-xs italic">Nenhuma</span>
                                            @endif
                                            <div>
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                    {{ $task->status }}
                                                </span>
                                            </div>
                                            <div class="text-right space-x-2">
                                                <button @click="editando = true"
                                                    class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">EDITAR</button>
                                                <form
                                                    action="{{ route('projects.tasks.destroy', [$project->id, $task->id]) }}"
                                                    method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 text-xs font-bold"
                                                        onclick="return confirm('Excluir?')">EXCLUIR</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </template>
                                <!-- Edição -->
                                <template x-if="editando">
                                    <td colspan="5" class="p-0">
                                        <form action="{{ route('projects.tasks.update', [$project->id, $task->id]) }}"
                                            method="POST"
                                            class="bg-yellow-50 dark:bg-yellow-900/20 p-4 border-l-4 border-yellow-400">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-5 gap-4 items-center">
                                                <input type="text" name="description"
                                                    value="{{ $task->description }}"
                                                    class="block w-full rounded-md border-gray-300 text-xs dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <input type="date" name="start_date" value="{{ $task->start_date }}"
                                                    class="w-full text-xs rounded-md border-gray-300 dark:bg-gray-800">
                                                <input type="date" name="end_date" value="{{ $task->end_date }}"
                                                    class="w-full text-xs mt-1 rounded-md border-gray-300 dark:bg-gray-800">
                                            </div>
                                            <div>
                                                <select name="predecessor_task_id"
                                                    class="w-full text-xs rounded-md border-gray-300 dark:bg-gray-800 shadow-sm">
                                                    <option value="">-- Sem Predecessora --</option>
                                                    @foreach ($project->tasks as $pTask)
                                                        {{-- Evita que a tarefa seja dependente de si mesma --}}
                                                        @if ($pTask->id != $task->id)
                                                            <option value="{{ $pTask->id }}"
                                                                {{ $task->predecessor_task_id == $pTask->id ? 'selected' : '' }}>
                                                                {{ $pTask->description }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <select name="status"
                                                    class="w-full text-xs rounded-md border-gray-300 dark:bg-gray-800">
                                                    <option value="Não Concluída"
                                                        {{ $task->status == 'Não Concluída' ? 'selected' : '' }}>
                                                        Não Concluída</option>
                                                    <option value="Concluída"
                                                        {{ $task->status == 'Concluída' ? 'selected' : '' }}>
                                                        Concluída</option>
                                                </select>
                                            </div>
                                            <div class="text-right space-x-2">
                                                <button type="submit"
                                                    class="bg-green-600 text-white px-3 py-1 rounded text-xs font-bold uppercase">SALVAR</button>
                                                <button type="button" @click="editando = false"
                                                    class="text-gray-500 text-xs font-bold uppercase">CANCELAR</button>
                                            </div>

                                        </form>
                                    </td>
                                </template>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
