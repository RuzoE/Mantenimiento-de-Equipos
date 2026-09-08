<?php

namespace App\Http\Requests\Equipos;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class EquipoRequest extends FormRequest
{
    /**
     * Permiso requerido para el formulario concreto.
     */
    abstract protected function permiso(): string;

    public function authorize(): bool
    {
        return $this->user()?->can($this->permiso()) ?? false;
    }

    protected function equipoActual(): ?Equipo
    {
        $param = $this->route('equipo');

        return $param instanceof Equipo ? $param : null;
    }

    /**
     * Reglas comunes a alta y edición. Las subclases añaden las reglas unique.
     *
     * @return array<string, mixed>
     */
    protected function reglasComunes(): array
    {
        return [
            'tipo_equipo_id' => ['required', Rule::exists('tipos_equipos', 'id')],
            'marca_id' => ['required', Rule::exists('marcas', 'id')],
            'modelo' => ['nullable', 'string', 'max:100'],
            'ubicacion_id' => ['required', Rule::exists('ubicaciones', 'id')],
            'responsable_id' => ['nullable', Rule::exists('responsables', 'id')],
            'estado' => ['required', Rule::enum(EstadoEquipo::class)],
            'fecha_adquisicion' => ['nullable', 'date', 'before_or_equal:today'],
            'fecha_garantia' => ['nullable', 'date', 'after_or_equal:fecha_adquisicion'],
            'procesador' => ['nullable', 'string', 'max:100'],
            'memoria_ram' => ['nullable', 'string', 'max:100'],
            'almacenamiento' => ['nullable', 'string', 'max:100'],
            'sistema_operativo' => ['nullable', 'string', 'max:100'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo_interno' => strtoupper(trim((string) $this->input('codigo_interno'))),
            'numero_serie' => filled($this->input('numero_serie')) ? trim((string) $this->input('numero_serie')) : null,
            'responsable_id' => filled($this->input('responsable_id')) ? $this->input('responsable_id') : null,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'codigo_interno' => 'código interno',
            'tipo_equipo_id' => 'tipo de equipo',
            'marca_id' => 'marca',
            'numero_serie' => 'número de serie',
            'ubicacion_id' => 'ubicación',
            'responsable_id' => 'responsable',
            'fecha_adquisicion' => 'fecha de adquisición',
            'fecha_garantia' => 'fin de garantía',
            'sistema_operativo' => 'sistema operativo',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_garantia.after_or_equal' => 'El fin de garantía no puede ser anterior a la fecha de adquisición.',
            'fecha_adquisicion.before_or_equal' => 'La fecha de adquisición no puede ser futura.',
        ];
    }
}
