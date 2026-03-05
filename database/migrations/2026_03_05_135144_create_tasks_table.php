<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->text('description'); // Obrigatório

            // Relacionamento com Projeto (Foreign Key)
            $table->foreignId('project_id')->constrained()->onDelete('restrict');

            // Datas Opcionais
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Tarefa Predecessora (Auto-relacionamento opcional)
            $table->foreignId('predecessor_task_id')->nullable()
                ->constrained('tasks')
                ->onDelete('restrict');// Impede exclusão se houver tarefa predecessora

            $table->enum('status', ['Concluída', 'Não Concluída'])->default('Não Concluída');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
