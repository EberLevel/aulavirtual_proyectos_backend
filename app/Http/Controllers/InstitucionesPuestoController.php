<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Puesto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\CvBank;

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
                'institucion_puesto.nombre as institucion_nombre',
                'area_puestos.plazo_os', // Nuevo campo
                'area_puestos.vinculo'   // Nuevo campo
            )
            ->orderByRaw('COALESCE(area_puestos.orden, 9999999) ASC')
            ->get();
    }

    public function getPuestosByDomain()
    {
        $domain_id = request()->query('domain_id') ?? null;
        $puestos = DB::table('area_puestos')
            ->join('institucion_area', 'area_puestos.area_id', '=', 'institucion_area.id')
            ->join('instituciones', 'institucion_area.institucion_id', '=', 'instituciones.id')
            ->leftJoin('modalidad_puesto', 'area_puestos.modalidad_posicion', '=', 'modalidad_puesto.id')
            ->leftJoin('puesto_continuidad', 'area_puestos.continuidad_id', '=', 'puesto_continuidad.id')
            ->leftJoin('puesto_permanencia', 'area_puestos.permanencia_id', '=', 'puesto_permanencia.id')
            ->where('instituciones.domain_id', $domain_id)
            ->select(
                'area_puestos.*',
                'instituciones.siglas as institucion_siglas',
                'modalidad_puesto.nombre as modalidad_posicion',
                'institucion_area.nombre as area',
                'instituciones.nombre as institucion',
                'puesto_continuidad.nombre as continuidad',
                'puesto_permanencia.nombre as permanencia',
                'area_puestos.plazo_os', // Nuevo campo
                'area_puestos.vinculo'   // Nuevo campo
            )
            ->orderByRaw('COALESCE(area_puestos.orden, 9999999) ASC')
            ->get();
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
                'dni' => 'sometimes|string|max:191',
                'plazo_os' => 'nullable|integer',
                'vinculo' => 'sometimes|in:Nombrado,Cas,Orden de Servicio,Reposicion Judicial', 
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
                'dni',
                'plazo_os', // Nuevo campo
                'vinculo',  // Nuevo campo
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
            $data = DB::table('cv_banks as cv')
                ->leftJoin('area_puestos as ap', 'cv.id', '=', 'ap.cv_id')
                ->leftJoin('institucion_area as ia', 'ap.area_id', '=', 'ia.id')
                ->leftJoin('instituciones as i', 'ap.institucion_id', '=', 'i.id')
                ->select(
                    'cv.estado_actual_id as estado_postulante',
                    'ap.estado as estado_puesto',
                    'ap.modalidad_posicion as color',
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
                    'ap.plazo_os', // Nuevo campo
                    'ap.vinculo'   // Nuevo campo
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

    public function massiveUpload(Request $request)
    {
        try {
            // Validar que se envíe un array de datos
            $data = $request->input('puestos', []);
            if (!is_array($data) || empty($data)) {
                return response()->json(['message' => 'No se proporcionaron datos para la subida masiva'], 400);
            }

            $domainId = $request->input('domain_id');
            if (!$domainId || !is_numeric($domainId)) {
                return response()->json(['message' => 'El domain_id es requerido y debe ser un número'], 400);
            }

            // Listas predefinidas para mapear valores
            $estadoActualLista = [
                'Ocupado' => 1,
                'Rotar' => 2,
                'Finalizar' => 3,
                'Priorizar' => 4,
                'En trámite' => 5,
                'Requerido' => 6,
                'Por Requerir' => 7,
                'Anulado' => 8,
                'Congelado' => 9,
            ];

            $continuidadLista = [
                'Continúa' => 1,
                'Disponible' => 2,
                'Reservado' => 3,
                'Observado' => 4,
                'Indefinido' => 5,
            ];

            $modalidadPosicionLista = [
                'Celeste' => 1,
                'Verde' => 2,
                'Azul' => 3,
                'Naranja' => 4,
                'Morado' => 5,
                'Plomo' => 6,
            ];

            $vinculoLista = [
                'Nombrado',
                'Cas',
                'Orden de Servicio',
                'Reposicion Judicial',
            ];

            $successCount = 0;
            $errorDetails = [];
            $batchSize = 100;

            for ($i = 0; $i < count($data); $i += $batchSize) {
                $batch = array_slice($data, $i, $batchSize);

                DB::beginTransaction();

                foreach ($batch as $index => $puesto) {
                    $offset = $i + $index + 1;

                    // Validar campo requerido temprano
                    if (empty($puesto['codigo_del_area'])) {
                        $errorDetails[] = [
                            'row' => $offset,
                            'errors' => ['El código del área es requerido'],
                        ];
                        continue;
                    }

                    // Construir el campo dependencia a partir de gerencia, direccion y oficina (si existen)
                    if (isset($puesto['gerencia']) || isset($puesto['direccion']) || isset($puesto['oficina'])) {
                        $dependenciaParts = array_filter([
                            $puesto['gerencia'] ?? '',
                            $puesto['direccion'] ?? '',
                            $puesto['oficina'] ?? ''
                        ]);
                        $puesto['dependencia'] = implode(' - ', $dependenciaParts);
                    }

                    // Mapear los campos del Excel a los de la base de datos
                    $puestoData = [
                        'codigo' => $puesto['codigo_del_puesto'] ?? null,
                        'nombre' => $puesto['objeto_del_servicio'] ?? null,
                        'descripcion_servicio' => $puesto['descripcion_del_servicio'] ?? null,
                        'estado' => isset($puesto['estado_actual_del_puesto']) && isset($estadoActualLista[$puesto['estado_actual_del_puesto']]) 
                            ? $estadoActualLista[$puesto['estado_actual_del_puesto']] 
                            : null,
                        'continuidad_id' => isset($puesto['continuidad']) && isset($continuidadLista[$puesto['continuidad']]) 
                            ? $continuidadLista[$puesto['continuidad']] 
                            : null,
                        'modalidad_posicion' => isset($puesto['colores']) && isset($modalidadPosicionLista[$puesto['colores']]) 
                            ? $modalidadPosicionLista[$puesto['colores']] 
                            : null,
                        'dni' => $puesto['dni'] ?? null,
                        'dependencia' => $puesto['dependencia'] ?? null,
                        'orden' => $puesto['n_orden'] ?? null,
                        'permanencia_id' => isset($puesto['vigente']) ? ($puesto['vigente'] === 'Sí' ? 1 : 3) : 1,
                        'fecha_nombramiento' => $puesto['fecha_de_emision'] ?? null,
                        'sueldo_promedio' => $puesto['monto_total_os'] ?? null,
                        'salario_minimo' => $puesto['salario'] ?? null,
                        'plazo_os' => $puesto['plazo_de_os'] ?? null,
                        'vinculo' => isset($puesto['vinculo']) && in_array($puesto['vinculo'], $vinculoLista) 
                            ? $puesto['vinculo'] 
                            : null,
                        'perfil' => $puesto['perfil'] ?? null,
                    ];

                    // Validar los datos del puesto
                    $validator = Validator::make($puestoData, [
                        'codigo' => 'required|string|max:191',
                        'nombre' => 'required|string|max:191',
                        'descripcion_servicio' => 'nullable|string|max:500',
                        'estado' => 'nullable|numeric|in:1,2,3,4,5,6,7,8,9',
                        'continuidad_id' => 'required|numeric|in:1,2,3,4,5',
                        'modalidad_posicion' => 'nullable|integer|in:1,2,3,4,5,6',
                        'dni' => 'nullable|string|max:191',
                        'dependencia' => 'nullable|string|max:191',
                        'orden' => 'nullable|string|max:191',
                        'permanencia_id' => 'required|numeric|exists:puesto_permanencia,id',
                        'fecha_nombramiento' => 'nullable|date',
                        'sueldo_promedio' => 'nullable|numeric',
                        'salario_minimo' => 'nullable|numeric',
                        'plazo_os' => 'nullable|integer',
                        'vinculo' => 'nullable|in:Nombrado,Cas,Orden de Servicio,Reposicion Judicial',
                        'perfil' => 'nullable|string',
                    ]);

                    if ($validator->fails()) {
                        Log::info('Validación fallida para fila ' . $offset, [
                            'puesto' => $puesto,
                            'errors' => $validator->errors()->all(),
                        ]);
                        $errorDetails[] = [
                            'row' => $offset,
                            'errors' => $validator->errors()->all(),
                        ];
                        continue;
                    }

                    // Buscar el institucion_id a partir del código de instituciones
                    $institucion = DB::table('instituciones')
                        ->where('codigo', $puesto['codigo_del_area'])
                        ->where('domain_id', $domainId)
                        ->first();

                    if (!$institucion) {
                        $errorDetails[] = [
                            'row' => $offset,
                            'errors' => ['No se encontró la institución con el código especificado: ' . $puesto['codigo_del_area']],
                        ];
                        continue;
                    }
                    $puestoData['institucion_id'] = $institucion->id;
                    Log::info('Institución encontrada', ['institucion_id' => $institucion->id, 'codigo' => $puesto['codigo_del_area']]);

                    // No asignar area_id, ya que no se usa
                    // $puestoData['area_id'] = null; // Explícitamente opcional, ya que area_id es nullable

                    // Verificar duplicados de código
                    $existingCode = DB::table('area_puestos')
                        ->where('codigo', $puestoData['codigo'])
                        ->first();
                    if ($existingCode) {
                        $errorDetails[] = [
                            'row' => $offset,
                            'errors' => ['El código ya está en uso: ' . $puestoData['codigo']],
                        ];
                        continue;
                    }

                    // Si se proporciona un DNI, buscar el cv_id
                    if (!empty($puestoData['dni'])) {
                        $cv = CvBank::where('identification_number', $puestoData['dni'])
                            ->where('domain_id', $domainId)
                            ->first();
                        if ($cv) {
                            $puestoData['cv_id'] = $cv->id;
                            Log::info('CV encontrado', ['cv_id' => $cv->id, 'dni' => $puestoData['dni']]);
                        } else {
                            Log::info('CV no encontrado, procediendo sin cv_id', ['dni' => $puestoData['dni']]);
                        }
                    }

                    // Registrar datos antes de la inserción
                    Log::info('Datos del puesto antes de insertar', ['puestoData' => $puestoData]);

                    // Insertar el puesto
                    DB::table('area_puestos')->insert($puestoData);
                    $successCount++;
                }

                DB::commit();
            }

            $response = [
                'message' => 'Subida masiva procesada',
                'success_count' => $successCount,
                'total_rows' => count($data),
            ];

            if (!empty($errorDetails)) {
                $response['errors'] = $errorDetails;
            }

            return response()->json($response, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en la subida masiva: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Error en la subida masiva: ' . $e->getMessage()], 500);
        }
    }
}