<?php

namespace App\Http\Requests\Mantenimientos;

use App\Enums\EstadoEquipo;
use App\Enums\TipoMantenimiento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

abstract class MantenimientoRequest extends FormRequest
{
    abstract protected function permiso(): string;

    public function authorize(): bool
    {
        return $this->user()?->can($this->permiso()) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::enum(TipoMantenimiento::class)],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'responsable_id' => ['nullable', Rule::exists('responsables', 'id')],
            'estado_antes' => ['nullable', Rule::enum(EstadoEquipo::class)],
            'estado_despues' => ['nullable', Rule::enum(EstadoEquipo::class)],
            'descripcion' => ['required', 'string', 'max:2000'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'actividades' => ['nullable', 'array'],
            'actividades.*' => ['nullable', 'string', 'max:255'],
            'evidencias' => ['nullable', 'array', 'max:10'],
            'evidencias.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'responsable_id' => filled($this->input('responsable_id')) ? $this->input('responsable_id') : null,
            'estado_despues' => filled($this->input('estado_despues')) ? $this->input('estado_despues') : null,
            'estado_antes' => filled($this->input('estado_antes')) ? $this->input('estado_antes') : null,
        ]);
    }

    /**
     * Actividades sin líneas vacías, reindexadas.
     *
     * @return array<int, string>
     */
    public function actividadesLimpias(): array
    {
        return Collection::make($this->input('actividades', []))
            ->map(fn ($a) => trim((string) $a))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tipo' => 'tipo de mantenimiento',
            'fecha' => 'fecha',
            'responsable_id' => 'técnico o responsable',
            'estado_antes' => 'estado antes',
            'estado_despues' => 'estado después',
            'descripcion' => 'descripción',
            'evidencias.*' => 'archivo de evidencia',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha.before_or_equal' => 'La fecha del mantenimiento no puede ser futura.',
            'evidencias.*.mimes' => 'Cada evidencia debe ser una imagen, PDF o documento de Office.',
            'evidencias.*.max' => 'Cada evidencia no puede superar los 5 MB.',
        ];
    }
}
