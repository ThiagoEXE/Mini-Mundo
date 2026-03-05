<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Importação necessária

class Project extends Model
{
    use HasFactory;

    // 1. Configurações de Dados (Propriedades)
    protected $fillable = [
        'name',
        'description',
        'status',
        'budget',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
    ];

    // 2. Relacionamentos (Métodos)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // 3. Regras de Negócio (Helpers)
    public function hasTasks(): bool
    {
        return $this->tasks()->exists();
    }
}