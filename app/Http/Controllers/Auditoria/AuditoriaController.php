<?php

namespace App\Http\Controllers\Auditoria;

use App\Enums\EventoAuditoria;
use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function index(Request $request): View
    {
        $termino = $request->string('buscar')->trim()->value();

        $auditorias = Auditoria::query()
            ->with('user')
            ->when($termino !== '', fn (Builder $q) => $q->where('descripcion', 'like', "%{$termino}%"))
            ->when($request->filled('modulo'), fn (Builder $q) => $q->where('modulo', $request->string('modulo')->value()))
            ->when($request->filled('evento'), fn (Builder $q) => $q->where('evento', $request->string('evento')->value()))
            ->when($request->filled('user_id'), fn (Builder $q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('desde'), fn (Builder $q) => $q->whereDate('created_at', '>=', $request->date('desde')))
            ->when($request->filled('hasta'), fn (Builder $q) => $q->whereDate('created_at', '<=', $request->date('hasta')))
            ->recientes()
            ->paginate(20)
            ->withQueryString();

        return view('auditoria.index', [
            'auditorias' => $auditorias,
            'modulos' => Auditoria::query()->distinct()->orderBy('modulo')->pluck('modulo'),
            'eventos' => EventoAuditoria::cases(),
            'usuarios' => User::whereIn('id', Auditoria::query()->whereNotNull('user_id')->distinct()->pluck('user_id'))
                ->orderBy('name')->get(['id', 'name']),
            'filtros' => $request->only(['buscar', 'modulo', 'evento', 'user_id', 'desde', 'hasta']),
        ]);
    }
}
