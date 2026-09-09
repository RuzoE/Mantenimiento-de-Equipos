<?php

namespace App\Http\Requests\Traslados;

use App\Enums\MotivoTraslado;
use App\Enums\Permiso;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrasladoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(Permiso::TrasladosCrear->value) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $equipo = $this->route('equipo');

        return [
            'ubicacion_destino_id' => [
                'required', 'integer',
                Rule::exists('ubicaciones', 'id'),
                Rule::notIn([$equipo->ubicacion_id]),
            ],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'motivo' => ['required', Rule::enum(MotivoTraslado::class)],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ubicacion_destino_id' => 'ubicación de destino',
            'motivo' => 'motivo del traslado',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ubicacion_destino_id.not_in' => 'El equipo ya se encuentra en esa ubicación.',
            'fecha.before_or_equal' => 'La fecha del traslado no puede ser futura.',
        ];
    }
}
