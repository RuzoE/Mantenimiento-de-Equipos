<?php

namespace App\Http\Controllers\Mantenimientos;

use App\Http\Controllers\Controller;
use App\Models\MantenimientoEvidencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvidenciaController extends Controller
{
    /**
     * Descarga controlada de una evidencia (disco privado).
     */
    public function show(MantenimientoEvidencia $evidencia): StreamedResponse
    {
        $this->authorize('view', $evidencia->mantenimiento);

        abort_unless(Storage::disk('local')->exists($evidencia->ruta), 404);

        return Storage::disk('local')->download($evidencia->ruta, $evidencia->nombre_original);
    }

    public function destroy(MantenimientoEvidencia $evidencia): RedirectResponse
    {
        $this->authorize('update', $evidencia->mantenimiento);

        Storage::disk('local')->delete($evidencia->ruta);
        $evidencia->delete();

        return back()->with('success', 'Evidencia eliminada.');
    }
}
