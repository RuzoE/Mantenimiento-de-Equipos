<?php

namespace App\Http\Requests\Programaciones;

use App\Enums\FrecuenciaMantenimiento;
use App\Enums\TipoMantenimiento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class ProgramacionRequest extends FormRequest
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
            'frecuencia' => ['required', Rule::enum(FrecuenciaMantenimiento::class)],
            'fecha_ultimo_mantenimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'fecha_ultimo_mantenimiento' => filled($this->input('fecha_ultimo_mantenimiento'))
                ? $this->input('fecha_ultimo_mantenimiento')
                : null,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tipo' => 'tipo de mantenimiento',
            'frecuencia' => 'frecuencia',
            'fecha_ultimo_mantenimiento' => 'fecha del último mantenimiento',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha_ultimo_mantenimiento.before_or_equal' => 'La fecha del último mantenimiento no puede ser futura.',
        ];
    }
}
