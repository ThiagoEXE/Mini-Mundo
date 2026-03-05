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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Obrigatório e único
            $table->text('description')->nullable(); // Opcional
            $table->enum('status', ['Ativo', 'Inativo'])->default('Ativo');
            $table->decimal('budget', 15, 2)->nullable(); // Orçamento opcional
            $table->timestamps(); // Cria created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
