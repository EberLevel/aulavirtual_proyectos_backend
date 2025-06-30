<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PagoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index($domain_id)
    {
        ini_set('max_execution_time', 120); // aumentar tiempo de ejecución

        $validator = Validator::make(
            ['domain_id' => $domain_id],
            ['domain_id' => 'required|numeric']
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pagos = Pago::with('estados')
            ->where("domain_id", "=", "$domain_id")
            ->get();

        if ($pagos->isEmpty()) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'No se encontraron pagos para este dominio.'
            ], 404);
        }

        return response()->json([
            'responseCode' => 200,
            'response' => $pagos
        ], 200);
    }

    public function getPaymentByStudent($domain_id, $pago_id)
    {
        $validator = Validator::make(
            [
                "domain_id" => $domain_id,
                "pago_id" => $pago_id
            ],
            [
                'domain_id' => 'numeric|exists:domains,id',
                'pago_id' => 'numeric|exists:pagos,id'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pago = Pago::where('id', $pago_id)
            ->where('domain_id', $domain_id)
            ->first();

        if (!$pago) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'El pago no existe o no pertenece a esta sede.'
            ], 404);
        }

        // Obtener alumnos vinculados al pago
        $alumnos = PagoAlumno::with('pago.estados', 'alumno.ciclo') // Relación con el modelo Alumno
            ->where('pago_id', $pago_id)
            ->get()
            ->map(function ($pagoAlumno) {
                return [
                    'id' => $pagoAlumno->alumno->id,
                    'codigo' => $pagoAlumno->alumno->codigo,
                    'nombres' => $pagoAlumno->alumno->nombres,
                    'apellidos' => $pagoAlumno->alumno->apellidos,
                    'dni' => $pagoAlumno->alumno->dni,
                    'ciclo_id' => $pagoAlumno->alumno->ciclo_id,
                    'voucher_pago' => $pagoAlumno->voucher_pago,
                    'fecha_pago' => $pagoAlumno->pago?->fecha_pago ?? null,
                    'estado_id' => $pagoAlumno->estado_id ?? null,
                    'estado' => $pagoAlumno->estado->nombre ?? null,
                    'estado_color' => $pagoAlumno->estado->color ?? null,
                    'ciclo' => $pagoAlumno->alumno->ciclo->nombre ?? 'Sin ciclo'
                ];
            });

        return response()->json([
            'responseCode' => 200,
            'pago' => $pago,
            'alumnos' => $alumnos,
        ]);
    }

    public function update(Request $request, $domain, $pago_id)
    {

        $validator = Validator::make(
            array_merge($request->all(), ['pago_id' => $pago_id]),
            [
                'pago_id'           => 'required|numeric|exists:pagos,id',
                'domain_id'         => 'required|numeric|exists:domains,id',
                'nombre'            => 'sometimes|string|max:255',
                'descripcion'       => 'sometimes|nullable|string',
                'monto'             => 'sometimes|numeric|min:0',
                'fecha_pago'        => 'sometimes|date',
                'fecha_vencimiento' => 'sometimes|date|after_or_equal:fecha_pago',
                'estado_id'         => 'sometimes|numeric|exists:estados,id',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message'      => 'Datos inválidos.',
                'errors'       => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();
        // 2. Buscamos el pago asegurándonos que pertenezca al domain_id del body
        $pago = Pago::where('id', $data['pago_id'])
            ->where('domain_id', $data['domain_id'])
            ->first();

        if (! $pago) {
            return response()->json([
                'responseCode' => 404,
                'message'     => 'Pago no encontrado para este dominio.'
            ], 404);
        }
        // 3. Actualizamos únicamente los campos que mandó el body
        $pago->update(
            collect($data)
                ->only(['nombre', 'descripcion', 'monto', 'fecha_pago', 'fecha_vencimiento', 'estado_id'])
                ->toArray()
        );
        return response()->json([
            'responseCode' => 200,
            'message'     => 'Pago actualizado con éxito',
            'data'        => $pago
        ], 200);
    }

    public function getPagosPorAlumno($domain_id, $alumno_id)
    {
        // Aumentar el tiempo máximo de ejecución (para evitar timeout por muchos datos)
        set_time_limit(120);

        // Validar solo el alumno_id, ya no usamos domain_id
        $validator = Validator::make(
            ['alumno_id' => $alumno_id],
            [
                'alumno_id'  => 'required|numeric|exists:alumnos,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Obtener los pagos con el estado específico del alumno
        $pagos = Pago::whereHas('pagoAlumnos', function ($query) use ($alumno_id) {
            $query->where('alumno_id', $alumno_id);
        })
            ->with(['pagoAlumnos' => function ($query) use ($alumno_id) {
                $query->where('alumno_id', $alumno_id)->with('estado');
            }])
            ->get()
            ->map(function ($pago) use ($alumno_id) {
                // Obtener el registro específico de pago_alumno
                $pagoAlumno = $pago->pagoAlumnos->first();

                return [
                    'id' => $pago->id,
                    'nombre' => $pago->nombre,
                    'descripcion' => $pago->descripcion,
                    'monto' => $pago->monto,
                    'fecha_pago' => $pago->fecha_pago,
                    'fecha_vencimiento' => $pago->fecha_vencimiento,
                    'domain_id' => $pago->domain_id,
                    // Estado específico del alumno (no del pago general)
                    'estado_id' => $pagoAlumno->estado_id ?? $pago->estado_id,
                    'estados' => [
                        'id' => $pagoAlumno->estado->id ?? null,
                        'nombre' => $pagoAlumno->estado->nombre ?? 'Sin estado',
                        'color' => $pagoAlumno->estado->color ?? '#gray-500'
                    ],
                    // Voucher específico del alumno
                    'voucher_pago' => $pagoAlumno->voucher_pago ?? null,
                ];
            });

        if ($pagos->isEmpty()) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'No se encontraron pagos asignados para este alumno.',
            ], 404);
        }

        return response()->json([
            'responseCode' => 200,
            'response' => $pagos
        ], 200);
    }



    public function destroy(Request $request, $domain, $pago_id)
    {
        // 1) Mezclamos el pago_id de la ruta con lo que venga en el body
        $data = array_merge($request->all(), ['pago_id' => $pago_id]);
    
        // 2) Validamos
        $validator = Validator::make($data, [
            'pago_id'   => 'required|numeric|exists:pagos,id',
            'domain_id' => 'required|numeric|exists:domains,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message'      => 'Datos inválidos.',
                'errors'       => $validator->errors(),
            ], 422);
        }
    
        // 3) Buscamos el pago que coincida con id y domain_id
        $pago = Pago::where('id', $data['pago_id'])
            ->where('domain_id', $data['domain_id'])
            ->first();
    
        if (!$pago) {
            return response()->json([
                'responseCode' => 404,
                'message'      => 'Pago no encontrado para este dominio.',
            ], 404);
        }
    
        // ✅ NUEVO: 4) Usar transacción para eliminar vínculos primero
        DB::beginTransaction();
        
        try {
            // Eliminar primero todos los vínculos con alumnos
            PagoAlumno::where('pago_id', $data['pago_id'])->delete();
            
            // Ahora eliminar el pago
            $pago->delete();
            
            DB::commit();
            
            return response()->json([
                'responseCode' => 200,
                'message'      => 'Pago eliminado con éxito.'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'responseCode' => 500,
                'message'      => 'Error al eliminar el pago.',
                'error'        => $e->getMessage()
            ], 500);
        }
    }


    public function uploadVoucher(Request $request, $domain, $pago_id)
    {
        // 1) valida con el facade Validator
        $validator = Validator::make($request->all(), [
            'domain_id'    => 'required|numeric|exists:domains,id',
            'voucher_pago' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message'      => 'Datos inválidos.',
                'errors'       => $validator->errors(),
            ], 422);
        }

        // 2) recupera los datos validados
        $data = $validator->validated();

        // 3) busca el pago
        $pago = Pago::where('id', $pago_id)
            ->where('domain_id', $data['domain_id'])
            ->firstOrFail();

        // 4) convierte a base64
        $file   = $request->file('voucher_pago');
        $base64 = 'data:' . $file->getMimeType() . ';base64,' .
            base64_encode(file_get_contents($file->getRealPath()));

        // 5) guarda
        $pago->voucher_pago = $base64;
        $pago->estado_id   = 2;
        $pago->save();

        // 6) responde
        return response()->json([
            'responseCode' => 200,
            'message'      => 'Comprobante subido con éxito.',
            'data'         => $pago->only('id', 'voucher_pago'),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_pago',
            'estado_id' => 'required|exists:estados,id',
            'domain_id' => 'required|exists:domains,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pago = Pago::create($validator->validated());

            return response()->json([
                'responseCode' => 201,
                'message' => 'Pago creado con éxito.',
                'data' => $pago,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 500,
                'message' => 'Error al crear el pago.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // public function assignPayment(Request $request)
    // {
    //     $data = $request->all();

    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'pago_id' => 'required|exists:pagos,id',
    //             'alumnos' => 'required|array',
    //             'alumnos.*' => 'required|exists:alumnos,id',
    //         ]
    //     );

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'responseCode' => 422,
    //             'message' => 'Datos inválidos.',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $pagoId = $data['pago_id'];
    //     $alumnos  = $data['alumnos'];
    //     $estado = $data['estado'];

    //     foreach ($alumnos as $alumnoId) {
    //         $exists = PagoAlumno::where('pago_id', $pagoId)
    //             ->where('alumno_id', $alumnoId)
    //             ->exists();

    //         if ($exists) {
    //             return response()->json([
    //                 'responseCode' => 422,
    //                 'message' => "El alumno con ID $alumnoId ya está asignado al pago.",
    //             ], 422);
    //         }
    //     }

    //     foreach ($alumnos as $alumnoId) {
    //         PagoAlumno::create(
    //             [
    //                 'pago_id' => $pagoId,
    //                 'alumno_id' => $alumnoId,
    //                 'estado_id' => $estado
    //             ]
    //         );
    //     }

    //     return response()->json([
    //         'responseCode' => 200,
    //         'message' => 'Pago asignado con éxito a los alumnos.',
    //     ]);
    // } 

    public function assignPayment(Request $request)
    {
        /* ---------- 1. VALIDACIÓN ---------- */
        $data = $request->all();

        $validator = Validator::make($data, [
            'pago_id'           => 'required|exists:pagos,id',
            'estado'            => 'required|numeric|exists:estados,id',
            'alumnos'           => 'required|array',
            'alumnos.*.id'      => 'required|exists:alumnos,id',
            'alumnos.*.checked' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message'      => 'Datos inválidos.',
                'errors'       => $validator->errors(),
            ], 422);
        }

        /* ---------- 2. VARIABLES ---------- */
        $pagoId  = $data['pago_id'];
        $estado  = $data['estado'];
        $alumnos = collect($data['alumnos']);

        /* ---------- 3. ALUMNOS YA VINCULADOS A ESTE PAGO ---------- */
        $vinculados = PagoAlumno::where('pago_id', $pagoId)
            ->pluck('alumno_id')
            ->toArray();

        /* ---------- 4. PROCESAMOS EN UNA TRANSACCIÓN ---------- */
        DB::beginTransaction();

        try {

            /* Para actualizar la columna checked en bloque */
            $idsMarcados   = [];  // quedarán con checked = 1
            $idsDesmarcados = [];  // quedarán con checked = 0

            foreach ($alumnos as $item) {
                $alumnoId = $item['id'];
                $checked  = (bool) $item['checked'];

                if ($checked) {

                    // ➕ crea vínculo si aún no existe
                    if (!in_array($alumnoId, $vinculados)) {
                        PagoAlumno::create([
                            'pago_id'   => $pagoId,
                            'alumno_id' => $alumnoId,
                            'estado_id' => $estado,
                        ]);
                    }

                    $idsMarcados[] = $alumnoId;
                } else {

                    // ➖ elimina vínculo si existía
                    if (in_array($alumnoId, $vinculados)) {
                        PagoAlumno::where('pago_id',  $pagoId)
                            ->where('alumno_id', $alumnoId)
                            ->delete();
                    }

                    $idsDesmarcados[] = $alumnoId;
                }
            }

            /* ---------- 5. ACTUALIZAMOS COLUMNA `checked` EN ALUMNOS ---------- */
            if (!empty($idsMarcados)) {
                DB::table('alumnos')
                    ->whereIn('id', $idsMarcados)
                    ->update(['checked' => 1]);
            }

            if (!empty($idsDesmarcados)) {
                DB::table('alumnos')
                    ->whereIn('id', $idsDesmarcados)
                    ->update(['checked' => 0]);
            }

            DB::commit();

            return response()->json([
                'responseCode' => 200,
                'message'      => 'Vinculaciones de pago actualizadas con éxito.',
            ], 200);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'responseCode' => 500,
                'message'      => 'Error al guardar los cambios.',
                'error'        => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadPaymentByStudent(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pago_id' => 'required|exists:pagos,id',
                'alumno_id' => 'required|exists:alumnos,id',
                'voucher_pago' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pagoAlumno = PagoAlumno::where('pago_id', $request->get('pago_id'))
            ->where('alumno_id', $request->get('alumno_id'))
            ->first();

        if (!$pagoAlumno) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'La relación entre el pago y el alumno no existe.',
            ], 404);
        }

        if (!empty($pagoAlumno->voucher_pago)) {
            return response()->json([
                'responseCode' => 409,
                'message' => 'El voucher ya fue subido anteriormente.',
            ], 409);
        }

        $file = $request->file('voucher_pago');
        $base64Image = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));

        $pagoAlumno->voucher_pago = $base64Image;
        $pagoAlumno->estado_id = 2;
        $pagoAlumno->save();

        return response()->json([
            'responseCode' => 200,
            'message' => 'Voucher subido con éxito.',
        ]);
    }

    // Reemplazar el método validPayment en PagoController
    public function validPayment(Request $request, $domain_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'alumno_id' => 'required|exists:alumnos,id',
                'pago_id' => 'required|exists:pagos,id',
                'estado_id' => 'sometimes|exists:estados,id', // ✅ NUEVO: permite cambiar estado
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Buscar el registro de pago del alumno
        $pagoAlumno = PagoAlumno::where('alumno_id', $request->alumno_id)
            ->where('pago_id', $request->pago_id)
            ->first();

        // Si no se encuentra el pago
        if (!$pagoAlumno) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'Pago no encontrado para este alumno.',
            ], 404);
        }

        // ✅ QUITAR LA VALIDACIÓN RESTRICTIVA - permitir cambiar entre estados
        // ✅ NUEVO: Usar el estado_id del request o default a 21 (PAGADO)
        $nuevoEstado = $request->get('estado_id', 21);

        // Actualizar el estado del pago
        $pagoAlumno->estado_id = $nuevoEstado;
        $pagoAlumno->save();

        $estadoTexto = $nuevoEstado == 21 ? 'validado' : 'actualizado';

        // Respuesta exitosa
        return response()->json([
            'responseCode' => 200,
            'message' => "Pago {$estadoTexto} exitosamente."
        ], 200);
    }

    public function eliminarVoucherAlumno(Request $request, $domain_id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pago_id' => 'required|exists:pagos,id',
                'alumno_id' => 'required|exists:alumnos,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pagoAlumno = PagoAlumno::where('pago_id', $request->get('pago_id'))
            ->where('alumno_id', $request->get('alumno_id'))
            ->first();

        if (!$pagoAlumno) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'La relación entre el pago y el alumno no existe.',
            ], 404);
        }

        // Eliminar el voucher y cambiar estado a PENDIENTE
        $pagoAlumno->voucher_pago = null;
        $pagoAlumno->estado_id = 2; // PENDIENTE
        $pagoAlumno->save();

        return response()->json([
            'responseCode' => 200,
            'message' => 'Voucher eliminado con éxito.',
        ]);
    }
}
