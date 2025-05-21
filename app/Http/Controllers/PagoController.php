<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PagoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    public function index($domain_id)
    {
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

    public function assignPayment(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make(
            $request->all(),
            [
                'pago_id' => 'required|exists:pagos,id',
                'alumnos' => 'required|array',
                'alumnos.*' => 'required|exists:alumnos,id',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pagoId = $data['pago_id'];
        $alumnos  = $data['alumnos'];
        $estado = $data['estado'];

        foreach ($alumnos as $alumnoId) {
            $exists = PagoAlumno::where('pago_id', $pagoId)
                ->where('alumno_id', $alumnoId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'responseCode' => 422,
                    'message' => "El alumno con ID $alumnoId ya está asignado al pago.",
                ], 422);
            }
        }

        foreach ($alumnos as $alumnoId) {
            PagoAlumno::create(
                [
                    'pago_id' => $pagoId,
                    'alumno_id' => $alumnoId,
                    'estado_id' => $estado
                ]
            );
        }

        return response()->json([
            'responseCode' => 200,
            'message' => 'Pago asignado con éxito a los alumnos.',
        ]);
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

    public function validPayment(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'alumno_id' => 'required|exists:alumnos,id',
                'pago_id' => 'required|exists:pagos,id',
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
                'success' => false,
                'message' => 'Pago no encontrado para este alumno.',
            ], 404);
        }

        // Validar el estado del pago (por ejemplo: "validado")
        if ($pagoAlumno->estado_id === 21) {
            return response()->json([
                'success' => false,
                'message' => 'El pago ya ha sido validado.',
            ], 400);
        }

        // Actualizar el estado del pago a "validado"
        $pagoAlumno->estado_id = 21;
        $pagoAlumno->save();

        // Respuesta exitosa
        return response()->json([
            'success' => true,
            'message' => 'Pago validado exitosamente.'
        ], 200);
    }
}
