<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstitucionesPuestoController extends Controller
{
    public function index()
    {
        $area_id = request()->query('area_id') ?? null;

        if (!$area_id) {
            return response()->json(['error' => 'El parámetro area_id es requerido'], 400);
        }

        return DB::table('area_puestos')
            ->leftJoin('institucion_area', 'area_puestos.area_id', '=', 'institucion_area.id')
            ->leftJoin('instituciones', 'institucion_area.institucion_id', '=', 'instituciones.id')
            ->leftJoin('instituciones as institucion_puesto', 'area_puestos.institucion_id', '=', 'institucion_puesto.id')
            ->leftJoin('puesto_continuidad', 'area_puestos.continuidad_id', '=', 'puesto_continuidad.id')
            ->leftJoin('puesto_permanencia', 'area_puestos.permanencia_id', '=', 'puesto_permanencia.id')
            ->where('area_puestos.area_id', $area_id) 
            ->orWhere('institucion_puesto.id', $area_id)
            ->select(
                'area_puestos.*',
                'puesto_continuidad.nombre as continuidad',
                'puesto_permanencia.nombre as permanencia',
                'institucion_area.nombre as area',
                'instituciones.siglas as institucion_siglas',
                'institucion_puesto.nombre as institucion_nombre'
            )
            ->orderByRaw('COALESCE(area_puestos.orden, 9999999) ASC')
            ->get();
    }
    public function getPuestosByDomain()
    {
        $domain_id = request()->query('domain_id') ?? null;
        $puestos = DB::table('area_puestos')->join('institucion_area', 'area_puestos.area_id', '=', 'institucion_area.id')->join('instituciones', 'institucion_area.institucion_id', '=', 'instituciones.id')->leftJoin('modalidad_puesto', 'area_puestos.modalidad_posicion', '=', 'modalidad_puesto.id')->leftJoin('puesto_continuidad', 'area_puestos.continuidad_id', '=', 'puesto_continuidad.id')->leftJoin('puesto_permanencia', 'area_puestos.permanencia_id', '=', 'puesto_permanencia.id')->where('instituciones.domain_id', $domain_id)->select(
                'area_puestos.*',
                'instituciones.siglas as institucion_siglas',
                'modalidad_puesto.nombre as modalidad_posicion',
                'institucion_area.nombre as area',
                'instituciones.nombre as institucion',
                'puesto_continuidad.nombre as continuidad',
                'puesto_permanencia.nombre as permanencia'
            )
            ->orderByRaw('COALESCE(area_puestos.orden, 9999999) ASC')->get();
        return response()->json($puestos);
    }
    public function store(Request $request)
    {
        try {
            $id = $request->input('puesto_id');
            $rules = [
                'codigo' => 'required|string|max:191',
                'nombre' => 'required|string|max:191',
                'telefono' => 'sometimes|string|max:191',
                'email' => 'sometimes|email|max:191',
                'salario_minimo' => 'sometimes|numeric',
                'salario_maximo' => 'sometimes|numeric',
                'diferencia' => 'sometimes|numeric',
                'nivel' => 'sometimes|string|max:191',
                'dependencia' => 'sometimes|string|max:191',
                'nivel_perfil' => 'sometimes|string|max:191',
                'sueldo_promedio' => 'sometimes|numeric',
                'formacion' => 'sometimes|string',
                'capacitacion' => 'sometimes|string',
                'experiencia_especifica' => 'sometimes|string',
                'experiencia_general' => 'sometimes|string',
                'estado' => 'sometimes|numeric',
                'nombre_nombrado' => 'sometimes|string|max:191',
                'descripcion_servicio' => 'sometimes|string|max:500',
                'orden' => 'sometimes|string|max:191',
                'institucion_id' => 'sometimes|exists:instituciones,id',
                'cv_id' => 'sometimes|nullable|exists:cv_banks,id',
                'modalidad_posicion' => 'nullable|integer',
                'continuidad_id' => 'sometimes|exists:puesto_continuidad,id',
                'permanencia_id' => 'sometimes|exists:puesto_permanencia,id',
                'modalidad' => 'sometimes|string|max:191',
                'fecha_nombramiento' => 'sometimes|nullable|date',
                'fecha_limite' => 'sometimes|nullable|date',
                'dias_restantes' => 'sometimes|integer',
                'perfil' => 'sometimes|string',
                'observaciones' => 'sometimes|string',
                'dni' => 'sometimes|string|max:191'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->only([
                'codigo',
                'nombre',
                'telefono',
                'email',
                'salario_minimo',
                'salario_maximo',
                'diferencia',
                'nivel',
                'dependencia',
                'nivel_perfil',
                'sueldo_promedio',
                'formacion',
                'capacitacion',
                'experiencia_especifica',
                'experiencia_general',
                'estado',
                'fecha_nombramiento',
                'nombre_nombrado',
                'descripcion_servicio',
                'orden',
                'institucion_id',
                'cv_id',
                'modalidad_posicion',
                'continuidad_id',
                'permanencia_id',
                'fecha_limite',
                'dias_restantes',
                'perfil',
                'observaciones',
                'dni'
            ]);

            if ($request->has('modalidad_id')) {
                $data['modalidad'] = $request->input('modalidad_id');
            }

            if ($id) {
                DB::table('area_puestos')->where('id', $id)->update($data);
                $puesto = DB::table('area_puestos')->where('id', $id)->first();
                return response()->json($puesto, 200);
            }

            $newId = DB::table('area_puestos')->insertGetId($data);
            $puesto = DB::table('area_puestos')->where('id', $newId)->first();
            return response()->json($puesto, 201);
        } catch (\Exception $e) {
            Log::error('Error al procesar el puesto: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar el puesto: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Verificar si el registro existe
            $puesto = DB::table('area_puestos')->where('id', $id)->first();
            if (!$puesto) {
                DB::rollBack();
                return response()->json(['message' => 'Puesto no encontrado'], 404);
            }

            // Eliminar el registro
            $affectedRows = DB::table('area_puestos')->where('id', $id)->delete();
            
            // Confirmar la transacción
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al eliminar el puesto: ' . $e->getMessage()], 500);
        }

        return response()->json(null, 204);
    }
    public function getLastId()
    {
        $puesto = DB::table('area_puestos')->orderBy('id', 'desc')->first();

        if ($puesto && !empty($puesto->codigo)) {
            // Extraer el número del código (remover el prefijo 'PP' si existe)
            $lastCode = preg_replace('/[^0-9]/', '', $puesto->codigo); // Obtiene solo los dígitos
            $nextNumber = (int)$lastCode + 1;
            // Formatear el nuevo código con 'PP' + 7 dígitos
            $newCode = 'PP' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
        } else {
            // Si no hay registros, empezar con PP0000001
            $newCode = 'PP0000001';
        }

        return response()->json($newCode);
    }
    public function getControlPuestos($postulante_id)
    {
        try {
            //get from cv_banks
            $data = DB::table('cv_banks as cv')
                ->leftJoin('area_puestos as ap', 'cv.id', '=', 'ap.cv_id')
                ->leftJoin('institucion_area as ia', 'ap.area_id', '=', 'ia.id')
                ->leftJoin('instituciones as i', 'ap.institucion_id', '=', 'i.id')
                ->select(
                    'cv.estado_actual_id as estado_postulante',
                    'ap.estado as estado_puesto',
                    'ap.modalidad as color',
                    'ap.dias_restantes',
                    'ap.continuidad_id',
                    'cv.code as codigo_postulante',
                    'cv.id as postulante_id',
                    'ap.orden as n_orden',
                    'ap.nombre as objeto_orden',
                    'ap.salario_minimo as salario',
                    'ap.fecha_limite',
                    'ap.nombre as objeto_servicio',
                    'ap.sueldo_promedio as monto_total',
                    'ap.fecha_nombramiento as fecha_ingreso',
                    'ap.codigo as codigo',
                    'i.nombre as nombre_institucion',
                    'i.direccion as dependencia',
                    DB::raw("CONCAT(ap.codigo, ' - ', i.nombre) as area"),
                )
                ->where('cv.id', $postulante_id)
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error al obtener los puestos: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los puestos'], 500);
        }
    }

    public function storeControlPuestos(Request $request)
    {
        try {
            $idPostulante = $request->input('id_postulante');
            DB::table('cv_banks')->where('id', $idPostulante)->update([
                'estado_actual_id' => $request->input('estado_actual_postulante_id'),
                'color_id' => $request->input('color_id'),
            ]);
            return response()->json(['message' => 'Postulante actualizado correctamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error al guardar ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los puestos'], 500);
        }
    }
}