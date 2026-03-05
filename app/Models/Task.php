<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'project_id',
        'start_date',
        'end_date',
        'predecessor_task_id',
        'status',
    ];

    /**
     * Relacionamento: Uma tarefa pertence a um projeto.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // 1. Quem eu sigo (Predecessora)
    public function predecessor(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'predecessor_task_id');
    }

    // 2. Quem me segue (Dependentes)
    public function dependents(): HasMany
    {
        return $this->hasMany(Task::class, 'predecessor_task_id');
    }

    // 3. Minha trava de segurança
    public function hasDependents(): bool
    {
        // Usa o relacionamento 'dependents' para checar se existe algum registro
        return $this->dependents()->exists();
    }
}
