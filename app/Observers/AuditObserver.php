<?php

namespace App\Observers;

use App\Enums\EventoAuditoria;
use App\Models\Auditoria;
use BackedEnum;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    private const IGNORAR = ['id', 'created_at', 'updated_at', 'password', 'remember_token'];

    public function created(Model $model): void
    {
        $this->registrar($model, EventoAuditoria::Creado);
    }

    public function updated(Model $model): void
    {
        $cambios = $this->diff($model);

        if ($cambios === []) {
            return;
        }

        $soloActivo = array_keys($cambios) === ['activo'];

        $evento = match (true) {
            $soloActivo && ! (bool) $cambios['activo']['despues'] => EventoAuditoria::Desactivado,
            $soloActivo && (bool) $cambios['activo']['despues'] => EventoAuditoria::Reactivado,
            default => EventoAuditoria::Actualizado,
        };

        $this->registrar($model, $evento, $cambios);
    }

    public function deleted(Model $model): void
    {
        $this->registrar($model, EventoAuditoria::Eliminado);
    }

    /**
     * @return array<string, array{antes: mixed, despues: mixed}>
     */
    private function diff(Model $model): array
    {
        $ignorar = array_merge(self::IGNORAR, $model->auditExcept());
        $cambios = [];

        foreach ($model->getChanges() as $campo => $nuevo) {
            if (in_array($campo, $ignorar, true)) {
                continue;
            }

            $cambios[$campo] = [
                'antes' => $this->normalizar($model->getRawOriginal($campo)),
                'despues' => $this->normalizar($nuevo),
            ];
        }

        return $cambios;
    }

    private function normalizar(mixed $valor): mixed
    {
        return match (true) {
            $valor instanceof BackedEnum => $valor->value,
            $valor instanceof DateTimeInterface => $valor->format('Y-m-d H:i:s'),
            default => $valor,
        };
    }

    /**
     * @param  array<string, mixed>  $cambios
     */
    private function registrar(Model $model, EventoAuditoria $evento, array $cambios = []): void
    {
        Auditoria::create([
            'user_id' => auth()->id(),
            'evento' => $evento,
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'modulo' => $model->auditModulo(),
            'descripcion' => "{$evento->verbo()} {$model->auditEtiqueta()}",
            'cambios' => $cambios !== [] ? $cambios : null,
            'ip' => request()->ip(),
        ]);
    }
}
