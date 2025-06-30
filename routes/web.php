<?php

use Illuminate\Support\Facades\Route;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/{domain}', 'middleware' => ['validate.domain']], function () use ($router) {

    $router->get('test', function () {
        dd(1);
    });
    $router->get('usuarios/{domain_id}', 'UsuarioController@index');
    $router->get('usuario/{id}', 'UsuarioController@getUser');
    $router->get('usuarios/entidades/{id}', 'UsuarioController@getEntidades');
    $router->post('usuarios', 'UsuarioController@store');
    $router->delete('usuarios/{id}', 'UsuarioController@destroy');
    $router->get('usuarios/permisos/{id}', 'UsuarioController@getPermisos');

    $router->post('login', 'LoginController@login');

    $router->get('maestros', 'MaestroController@index');
    $router->post('maestros', 'MaestroController@store');
    $router->get('maestros/{id}', 'MaestroController@show');
    $router->put('maestros/{id}', 'MaestroController@update');
    $router->delete('maestros/{id}', 'MaestroController@destroy');


    $router->get('parametros', 'ParametroController@index');
    $router->post('parametros', 'ParametroController@store');
    $router->get('parametros/{id}', 'ParametroController@show');
    $router->put('parametros/{id}', 'ParametroController@update');
    $router->delete('parametros/{id}', 'ParametroController@destroy');
    $router->get('parametrosAll/{domain_id}', 'ParametroController@indexAll');
    $router->get('parametrosRecursive/{domain_id}', 'ParametroController@indexRecursive');

    //informacion academica
    $router->get('informacion-academica/data-create/{domain_id}', 'InformacionAcademicaController@getDataCreate');
    $router->get('informacion-academica/{id}', 'InformacionAcademicaController@show');
    $router->get('informacion-academica/domain/{domain_id}', 'InformacionAcademicaController@getByDomainId');
    $router->get('informacion-academica', 'InformacionAcademicaController@index');
    $router->post('informacion-academica', 'InformacionAcademicaController@store');
    $router->post('informacion-academica/{id}', 'InformacionAcademicaController@update');
    $router->delete('informacion-academica/{id}', 'InformacionAcademicaController@destroy');
    $router->post('informacion-academica/{id}/validado', 'InformacionAcademicaController@updateValidado');

    // Capacitaciones Postulante
    $router->get('capacitaciones-postulante/data-create/{domain_id}', 'CapacitacionesPostulanteController@getDataCreate');
    $router->post('capacitaciones-postulante', 'CapacitacionesPostulanteController@store');
    $router->post('capacitaciones-postulante/{id}/validado', 'CapacitacionesPostulanteController@updateValidado');
    $router->put('capacitaciones-postulante/{id}', 'CapacitacionesPostulanteController@update');
    $router->get('capacitaciones-postulante/{id_postulante}', 'CapacitacionesPostulanteController@index');
    $router->delete('capacitaciones-postulante/{id}', 'CapacitacionesPostulanteController@destroy');

    // Experiencia Laboral
    $router->get('experiencia-laboral/data-create/{domain_id}', 'ExperienciaLaboralController@getDataCreate');
    $router->post('experiencia-laboral', 'ExperienciaLaboralController@store');
    $router->post('experiencia-laboral/{id}/validado', 'ExperienciaLaboralController@updateValidado');
    $router->put('experiencia-laboral/{id}', 'ExperienciaLaboralController@update');
    $router->get('experiencia-laboral/{id_postulante}', 'ExperienciaLaboralController@index');
    $router->delete('experiencia-laboral/{id}', 'ExperienciaLaboralController@destroy');

    // Experiencia Especifica
    $router->get('experiencia-especifica/data-create/{domain_id}', 'ExperienciaEspecificaController@getDataCreate');
    $router->post('experiencia-especifica', 'ExperienciaEspecificaController@store');
    $router->post('experiencia-especifica/{id}/validado', 'ExperienciaEspecificaController@updateValidado');
    $router->put('experiencia-especifica/{id}', 'ExperienciaEspecificaController@update');
    $router->get('experiencia-especifica/{id_postulante}', 'ExperienciaEspecificaController@index');
    $router->delete('experiencia-especifica/{id}', 'ExperienciaEspecificaController@destroy');

    // Referencias Laborales
    $router->get('referencias-laborales/{id_postulante}', 'ReferenciasLaboralesController@index');
    $router->post('referencias-laborales', 'ReferenciasLaboralesController@store');
    $router->put('referencias-laborales/{id}', 'ReferenciasLaboralesController@update');
    $router->delete('referencias-laborales/{id}', 'ReferenciasLaboralesController@destroy');

    // Rutas para el FormularioFinalPostulante
    $router->get('formulario-final-postulante/data-create', 'FormularioFinalPostulanteController@getDataCreate');
    $router->get('formulario-final-postulante', 'FormularioFinalPostulanteController@index');
    $router->post('formulario-final-postulante', 'FormularioFinalPostulanteController@store');
    $router->put('formulario-final-postulante/{id}', 'FormularioFinalPostulanteController@update');
    $router->delete('formulario-final-postulante/{id}', 'FormularioFinalPostulanteController@destroy');


    // Referencias Familiares
    $router->post('referencias-familiares', 'ReferenciasFamiliaresController@store');
    $router->put('referencias-familiares/{id}', 'ReferenciasFamiliaresController@update');
    $router->get('referencias-familiares/{id_postulante}', 'ReferenciasFamiliaresController@index');
    $router->delete('referencias-familiares/{id}', 'ReferenciasFamiliaresController@destroy');


    $router->get('instituciones', 'InstitucionesController@index');
    $router->get('instituciones/{id:[0-9]+}', 'InstitucionesController@show');

    $router->get('instituciones/getAreas', 'InstitucionAreaController@index');

    $router->get('instituciones/areas/{id}', 'InstitucionAreaController@getByAreaId');
    $router->post('instituciones/areas', 'InstitucionAreaController@store');
    $router->delete('instituciones/areas/{id}', 'InstitucionAreaController@destroy');

    $router->get('instituciones/reporte-puestos', 'InstitucionesPuestoController@getPuestosByDomain');
    $router->get('instituciones/puestos', 'InstitucionesPuestoController@index');
    $router->post('instituciones/puestos', 'InstitucionesPuestoController@store');
    $router->get('instituciones/puestos/get-last-id', 'InstitucionesPuestoController@getLastId');
    $router->post('control-puestos', 'InstitucionesPuestoController@storeControlPuestos');
    $router->get('control-puestos/{postulante_id}', 'InstitucionesPuestoController@getControlPuestos');
    $router->delete('instituciones/puestos/{id}', 'InstitucionesPuestoController@destroy');
    $router->post('instituciones/puestos/massiveUpload', 'InstitucionesPuestoController@massiveUpload');


    $router->get('instituciones/puestos/perfiles', 'InstitucionesPerfilController@index');
    $router->post('instituciones/puestos/perfiles', 'InstitucionesPerfilController@store');
    $router->delete('instituciones/puestos/perfiles/{id}', 'InstitucionesPerfilController@destroy');
    $router->post('instituciones/get-institucion', 'InstitucionesController@getInstitucion');
    $router->post('instituciones', 'InstitucionesController@store');
    $router->post('instituciones/massiveUpload', 'InstitucionesController@massiveUpload');
    $router->put('instituciones/{id}', 'InstitucionesController@update');
    $router->delete('instituciones/{id}', 'InstitucionesController@destroy');
    $router->get('cv', 'InstitucionesController@getCv');
    $router->get('institutions-dropdown', 'InstitucionesController@dropdown');
    $router->get('subinstitutions-dropdown', 'InstitucionesController@getInstitucionesByDomain');
    $router->get('permanencia-dropdown', 'InstitucionesController@getPermanenciaDropdown');
    $router->get('continuidad-dropdown', 'InstitucionesController@getContinuidadDropdown');
    //post control-puestos
    $router->get('carreras/{id}', 'CarreraController@show');
    $router->get('carreras-dropdown', 'CarreraController@dropdown');
    $router->get('carreras-dropdown/{plan_de_estudios_id}', 'CarreraController@dropDown');
    $router->get('carreras-list/{dominio_id}', 'CarreraController@index');

    $router->post('carreras', 'CarreraController@store');
    $router->put('carreras/{id}', 'CarreraController@update');
    $router->delete('carreras/{id}', 'CarreraController@destroy');

    //get ciclos dropdown
    $router->get('ciclos-dropdown', 'ParametroController@dropdown');
    $router->get('ciclos-dropdown/{domain_id}', 'CicloController@dropDown');


    // DOCUMENTO GESTION RALVA
    $router->get('documento-gestion/{domain_id}', 'DocumentoGestionController@index');
    $router->post('documento-gestion', 'DocumentoGestionController@store');
    $router->get('documento-gestion/{domain_id}/{id}', 'DocumentoGestionController@show');
    $router->put('documento-gestion/{id}', 'DocumentoGestionController@update');
    $router->put('documento-gestion-eliminar/{id}', 'DocumentoGestionController@destroy');
    $router->get('documento-gestion-codigo', 'DocumentoGestionController@generateCode');
    $router->get('alumnos/{domain_id}/{user_id}/documentos', 'DocumentoGestionController@getAlumnoDocuments');

    //Docente
    $router->get('docentes-dropdown/{domain_id}', 'DocenteController@dropdown');

    $router->get('docentes/logged/{docente_id}/{dominio}', 'DocenteController@getLoggedDocente');
    $router->get('docentes/imagen', 'DocenteController@imagen');
    $router->get('docentes/listar/{domain_id}', 'DocenteController@index');
    $router->get('docentes/listar/{domain_id}/{id}', 'DocenteController@show');
    $router->post('docentes/registrar', 'DocenteController@store');
    $router->post('docentes-masivos', 'DocenteController@storeMasivos');
    $router->put('docentes/actualizar/{id}', 'DocenteController@update');
    $router->delete('docentes/eliminar/{id}', 'DocenteController@destroy');

    $router->get('cvbanksByDominio/{domain_id}', 'CvBankController@index');
    $router->get('cvbanks/filters-data', 'CvBankController@filtersData');
    $router->get('cvbanks/create-data/{domain_id}', 'CvBankController@dataCreate');
    $router->post('cvbanks', 'CvBankController@store');
    $router->post('cv-banks/massiveUpload', 'CvBankController@massiveUpload');
    $router->get('cvbanks/{id}', 'CvBankController@show');
    $router->get('cvbanks/user/{id}', 'CvBankController@showByUser');
    $router->post('cvbanks/{id}', 'CvBankController@update');
    $router->delete('cvbanks/{id}', 'CvBankController@destroy');


    $router->post('cursos', 'CursoController@store');
    $router->post('cursos-masivos', 'CursoController@storeMasivos');
    $router->put('cursos/{id}', 'CursoController@update');
    $router->delete('cursos/{id}', 'CursoController@destroy');
    $router->get('cursos/{id}/tema', 'CursoController@getTema');
    $router->get('cursos', 'CursoController@index');
    $router->get('cursos/{id}', 'CursoController@show');
    $router->get('cursos/alumno/{alumnoId}', 'CursoAlumnoController@indexByPlan');
    $router->get('cursos/domain/{domainId}', 'CursoController@getCursosByDomain');
    $router->get('cursos/{id}/syllabus', 'CursoController@getSyllabus');
    $router->get('cursos/carrera/{id}', 'CursoController@index');
    $router->get('unidades/all/{domain_id}', 'CursoController@getAllCursos');
    $router->get('unidades/{domain_id}', 'CursoController@getTotalCreditsByCurso');
    $router->get('cursos/plan-estudio/{planEstudioId}/{carreraId}', 'CursoController@getCursosByPlanEstudioYCarrera');

    $router->get('cursos/plan-estudio/{planEstudioId}/{carreraId}/{alumnoId}', 'CursoController@getCursosByPlanEstudioYCarrera');

    $router->get('roles/{domain_id}/{rol_id}', 'RolController@index');
    $router->post('rol/guardar', 'RolController@store');
    $router->get('rol/{id}', 'RolController@show');
    $router->put('rol/guardar/{id}', 'RolController@update');
    $router->delete('rol/eliminar/{id}', 'RolController@destroy');
    $router->get('roles-dropdown/{domain_id}/{rol_id}', 'RolController@getRolesDropDown');

    $router->post('rol/guardar-permiso', 'RolController@guardarPermiso');
    $router->get('rol/get-rol-permiso/{id}/{domain_id}', 'RolController@getRolPermisos');
    $router->get('rol/get-rol-permiso-admin/{id}/{domain_id}', 'RolController@getRolPermisosAdmin');
    $router->get('rol/get-rol-permiso-admin-dominio/{id}/{domain_id}', 'RolController@getRolPermisosAdminDominio');
    $router->get('empresas', 'EmpresaController@index');
    $router->post('empresa/guardar', 'EmpresaController@store');
    $router->get('empresa/{id}', 'EmpresaController@show');
    $router->put('empresa/guardar/{id}', 'EmpresaController@update');
    $router->delete('empresa/eliminar/{id}', 'EmpresaController@destroy');
    $router->get('empresas-dropdown', 'EmpresaController@dropdown');
    $router->get('permisos/{domain_id}', 'PermisoController@index');
    $router->post('permiso/guardar', 'PermisoController@store');

    $router->get('capacitaciones', 'CapacitacionController@index');
    $router->post('capacitaciones', 'CapacitacionController@store');
    $router->get('capacitaciones/{id}', 'CapacitacionController@show');
    $router->put('capacitaciones/{id}', 'CapacitacionController@update');
    $router->put('capacitaciones-eliminar/{id}', 'CapacitacionController@destroy');
    $router->get('capacitaciones-codigo', 'CapacitacionController@generateCode');
    $router->get('capacitaciones-docentes/{domain_id}', 'CapacitacionController@listarDocentes');
    $router->get('capacitaciones-estados/{domain_id}', 'CapacitacionController@listarEstados');
    // para pagos
    $router->get('pagos/{domain_id}', 'PagoController@index');
    $router->get('pagos/{domain_id}/{pago_id}/alumnos', 'PagoController@getPaymentByStudent');
    $router->post('pagos', 'PagoController@store');
    $router->post('asignar-pagos', 'PagoController@assignPayment');
    $router->post('pagos-por-alumnos', 'PagoController@uploadPaymentByStudent');
    $router->post('pagos/{domain_id}/validar-pago', 'PagoController@validPayment');

    $router->put('pagos/{pago_id}', 'PagoController@update');
    $router->delete('pagos/{pago_id}', 'PagoController@destroy');
    $router->post('pagos/{pago_id}/upload-voucher','PagoController@uploadVoucher');
    $router->get('pagos-alumno/{domain_id}/{alumno_id}', 'PagoController@getPagosPorAlumno');


    $router->delete('pagos/{pago_id}', 'PagoController@destroy');
    $router->post('pagos/{pago_id}/upload-voucher','PagoController@uploadVoucher');

    $router->get('grupo-de-evaluaciones/{curso_id}', 'GrupoDeEvaluacionesController@index');
    $router->post('grupo-de-evaluaciones', 'GrupoDeEvaluacionesController@store');
    $router->put('grupo-de-evaluaciones/{id}', 'GrupoDeEvaluacionesController@update');
    $router->delete('grupo-de-evaluaciones/{id}', 'GrupoDeEvaluacionesController@destroy');




    $router->get('alumnos/logged/{alumno_id}/{dominio}', 'AlumnoController@getLoggedAlumno');
    $router->get('alumnos/{dominio}', 'AlumnoController@index');
    $router->post('alumnos', 'AlumnoController@store');
    $router->post('alumnos-masivos', 'AlumnoController@storeMasivos');
    $router->get('/alumno/{id}', 'AlumnoController@show');
    $router->put('alumnos/{id}/{domain_id}', 'AlumnoController@update');
    $router->delete('alumnos/{id}/{dominio}', 'AlumnoController@destroy');
    $router->post('alumnos/{id}/{dominio}', 'AlumnoController@paymentByStudent');
    $router->post('alumnos/subir-comprobante', 'AlumnoController@subirComprobante');


    //horario routes
    $router->get('horario', 'HorarioController@index');
    $router->post('horario', 'HorarioController@store');
    $router->get('horario/{id}', 'HorarioController@show');
    //participantes routes
    $router->get('participantes/{domain_id}/{curso_id}', 'ParticipanteController@show');
    $router->post('participantes', 'ParticipanteController@store');
    $router->get('evaluacionesByalumnos/promedio/{curso_id}/{alumno_id}', 'ParticipanteController@getPromedioEvaluaciones');
    $router->get('cursos/promedio/{curso_id}', 'ParticipanteController@getPromedioCurso');
    $router->put('participante-estado-update',  'ParticipanteController@updateEstadoCursoAlumno');

    //asistencia routes
    $router->get('asistencia-curso', 'AsistenciaCursoController@show');
    $router->post('asistencia-curso-marcar', 'AsistenciaCursoController@store');
    $router->get('get-fechas-curso-horario', 'AsistenciaCursoController@getFechasCursoHorario');

    //evaluacion routes
    $router->post('evaluaciones', 'EvaluacionesController@store');
    $router->get('evaluaciones/{id}', 'EvaluacionesController@index');
    $router->post('evaluaciones/recursos/{evaluacion_id}', 'EvaluacionesController@subirRecursos');
    $router->put('evaluaciones/{id}', 'EvaluacionesController@update');
    $router->delete('evaluaciones/{id}', 'EvaluacionesController@destroy');
    $router->get('evaluaciones/alumno/{alumnoId}/{grupoId}', 'EvaluacionesController@getNotasPorAlumnoYGrupo');
    $router->get('evaluacionesByAlumno/alumno/{alumnoId}/{grupoId}', 'EvaluacionesController@getPromedioPorAlumnoYGrupo');
    $router->get('evaluacion/{id}', 'EvaluacionesController@getEvaluacionById');
    $router->post('evaluacion/{id}', 'EvaluacionesController@updateEvaluacionById');
    $router->get('evaluacionesBygrupo/grupo/{grupoId}/{alumnoId}', 'EvaluacionesController@getEvaluacionesPorGrupo');


    //calendarios routes
    $router->post('calendario/alumno', 'CalendarioController@getAlumnoCalendario');
    $router->post('calendario/docente', 'CalendarioController@getDocenteCalendario');

    // Grado de Instrucción
    $router->get('grado-instrucciones/{domain_id}', 'GradoInstruccionController@index');
    $router->post('grado-instruccion', 'GradoInstruccionController@store');
    $router->get('grado-instruccion/{id}', 'GradoInstruccionController@show');
    $router->put('grado-instruccion/{id}', 'GradoInstruccionController@update');
    $router->delete('grado-instruccion/{id}', 'GradoInstruccionController@destroy');

    // Vinculo Laboral
    $router->get('vinculos-laborales/{domain_id}', 'VinculoLaboralController@index');
    $router->post('vinculo-laboral', 'VinculoLaboralController@store');
    $router->get('vinculo-laboral/{id}', 'VinculoLaboralController@show');
    $router->put('vinculo-laboral/{id}', 'VinculoLaboralController@update');
    $router->delete('vinculo-laboral/{id}', 'VinculoLaboralController@destroy');

    // Nivel de Cargo
    $router->get('niveles-cargo/{domain_id}', 'NivelCargoController@index');
    $router->post('nivel-cargo', 'NivelCargoController@store');
    $router->get('nivel-cargo/{id}', 'NivelCargoController@show');
    $router->put('nivel-cargo/{id}', 'NivelCargoController@update');
    $router->delete('nivel-cargo/{id}', 'NivelCargoController@destroy');

    // Modalidades de Puesto
    $router->get('modalidades-puesto/{domain_id}', 'ModalidadPuestoController@index');
    $router->post('modalidad-puesto', 'ModalidadPuestoController@store');
    $router->get('modalidad-puesto/{id}', 'ModalidadPuestoController@show');
    $router->put('modalidad-puesto/{id}', 'ModalidadPuestoController@update');
    $router->delete('modalidad-puesto/{id}', 'ModalidadPuestoController@destroy');
    $router->get('modalidad-puesto-dropdown', 'ModalidadPuestoController@getDropdown');
    //Profesiones
    $router->get('profesiones/{domain_id}', 'ProfesionController@index');
    $router->post('profesion', 'ProfesionController@store');
    $router->post('profesiones/massive', 'ProfesionController@massiveStore');
    $router->get('profesion/{id}', 'ProfesionController@show');
    $router->put('profesion/{id}', 'ProfesionController@update');
    $router->delete('profesion/{id}', 'ProfesionController@destroy');

    // Rutas para EstadoAvance
    $router->get('estados-avance/{domain_id}', 'EstadoAvanceController@index');
    $router->post('estado-avance', 'EstadoAvanceController@store');
    $router->get('estado-avance/{id}', 'EstadoAvanceController@show');
    $router->put('estado-avance/{id}', 'EstadoAvanceController@update');
    $router->delete('estado-avance/{id}', 'EstadoAvanceController@destroy');
    $router->get('estado-actual-dropdown', 'EstadoActualController@getEstadoActualDropdown');
    //Escala
    $router->get('escalas/{domain_id}', 'EscalaController@index');
    $router->post('escala', 'EscalaController@store');
    $router->get('escala/{id}', 'EscalaController@show');
    $router->put('escala/{id}', 'EscalaController@update');
    $router->delete('escala/{id}', 'EscalaController@destroy');

    //Año - Afiliado al Paritdo en Frontend
    $router->get('anos/{domain_id}', 'AnoController@index');
    $router->post('ano', 'AnoController@store');
    $router->get('ano/{id}', 'AnoController@show');
    $router->put('ano/{id}', 'AnoController@update');
    $router->delete('ano/{id}', 'AnoController@destroy');

    //Ocupacion
    $router->get('ocupaciones-actuales/{domain_id}', 'OcupacionActualController@index');
    $router->post('ocupacion-actual', 'OcupacionActualController@store');
    $router->get('ocupacion-actual/{id}', 'OcupacionActualController@show');
    $router->put('ocupacion-actual/{id}', 'OcupacionActualController@update');
    $router->delete('ocupacion-actual/{id}', 'OcupacionActualController@destroy');

    //preguntas routes
    $router->get('preguntas/{domain_id}/{evaluacion_id}', 'PreguntaController@index');
    $router->post('preguntas', 'PreguntaController@store');
    $router->get('preguntas/{id}', 'PreguntaController@show');
    $router->put('preguntas/{id}', 'PreguntaController@update');
    $router->delete('preguntas/{id}', 'PreguntaController@destroy');
    $router->post('foros', 'ForoController@store');
    $router->get('foros/{domain_id}/{alumno_id}/{docente_id}', 'ForoController@show');
    $router->post('foros/message', 'ForoController@storeMessage');
    //areas de formacion
    $router->get('areas-de-formacion/{domain_id}', 'AreaDeFormacionController@index');
    $router->post('areas-de-formacion/{domain_id}', 'AreaDeFormacionController@store');
    $router->put('areas-de-formacion/{domain_id}/{id}', 'AreaDeFormacionController@update');
    $router->delete('areas-de-formacion/{domain_id}/{id}', 'AreaDeFormacionController@destroy');


    //modulos formativos
    $router->get('modulos-formativos/{domain_id}', 'ModuloFormativoController@index');
    $router->post('modulos-formativos/{domain_id}', 'ModuloFormativoController@store');
    $router->put('modulos-formativos/{domain_id}/{id}', 'ModuloFormativoController@update');
    $router->delete('modulos-formativos/{domain_id}/{id}', 'ModuloFormativoController@destroy');

    //ciclos
    $router->get('ciclos/{domain_id}', 'CicloController@index');
    $router->post('ciclos/{domain_id}', 'CicloController@store');
    $router->put('ciclos/{domain_id}/{id}', 'CicloController@update');
    $router->delete('ciclos/{domain_id}/{id}', 'CicloController@destroy');
    $router->post('ciclos-orden', 'CicloController@orden');

    //Promociones
    $router->get('promociones/{domain_id}', 'PromocionController@index');
    $router->post('promociones', 'PromocionController@store');
    $router->get('promocion/{id}', 'PromocionController@show');
    $router->put('promociones/{id}', 'PromocionController@update');
    $router->delete('promociones/{id}', 'PromocionController@destroy');

    //estado
    $router->get('estados/{domain_id}', 'EstadoController@index');
    $router->get('estados/{domain_id}/validate-payment', 'EstadoController@validatePayment');
    $router->post('estados/{domain_id}', 'EstadoController@store');
    $router->put('estados/{domain_id}/{id}', 'EstadoController@update');
    $router->delete('estados/{domain_id}/{id}', 'EstadoController@destroy');

    //estado de curso
    $router->get('estados-curso/{domain_id}', 'EstadoCursoController@index');
    $router->post('estados-curso/{domain_id}', 'EstadoCursoController@store');
    $router->put('estados-curso/{domain_id}/{id}', 'EstadoCursoController@update');
    $router->delete('estados-curso/{domain_id}/{id}', 'EstadoCursoController@destroy');
    $router->get('planes/carrera/{carrera_id}', 'EstadoCursoController@listarPlanPorCarrera');

    //aulas
    $router->get('aulas/{dominio_id}', 'AulaController@index');
    $router->post('aulas', 'AulaController@store');
    $router->delete('aulas/{id}', 'AulaController@destroy');
    $router->post('aulas/disponibilidad', 'AulaController@saveDisponibilidad');
    $router->get('aulas/disponibilidad/{aula_id}', 'AulaController@getDisponibilidad');
    $router->delete('aulas/disponibilidad/{id}', 'AulaController@destroyDisponibilidad');

    //get institution data
    $router->get('company/{domain_id}', 'CompanyController@show');
    $router->post('company', 'CompanyController@store');



    //cursos
    $router->get('cursos-docente/{docente_id}', 'CursoDocenteController@index');
    $router->get('cursos-alumno/{alumno_id}', 'CursoAlumnoController@index');
    $router->get('cursosByAlumno/{alumno_id}', 'CursoAlumnoController@indexByAlumno');
    $router->put('curso/estado', 'CursoAlumnoController@updateCursoEstado');

    //alumno preguntas
    $router->post('alumno-preguntas', 'PreguntaAlumnoController@guardarAlumnoPregunta');
    $router->get('alumno-preguntas/{preguntaAlumnoId}', 'PreguntaAlumnoController@obtenerPreguntaAlumno');
    $router->get('preguntasByAlumno/{preguntaId}', 'PreguntaAlumnoController@obtenerAlumnosPorPreguntaId');
    $router->put('pregunta-alumno', 'PreguntaAlumnoController@actualizarEstado');
    $router->get('suma-calificaciones', 'PreguntaAlumnoController@obtenerSumaCalificaciones');





    //ObteberAlumnoPorEvaluacion
    $router->get('evaluacionesByalumnos/{id}', 'EvaluacionesByModalidadController@obtenerAlumnosPorEvaluacion');
    $router->post('evaluacionesByalumnos/guardarNotas', 'EvaluacionesByModalidadController@guardarNotas');


    Route::get('cursos/{curso_id}/evaluaciones', 'PreguntaAlumnoController@obtenerCursosConEvaluaciones');
    Route::get('obtener-preguntas-corregidas/{pregunta_id}', 'PreguntaAlumnoController@obtenerPreguntasNoCorregidas');

    //Ceiber Conrago Garibay Choque - 2024-08-10 Subgrupo de rutas para las apis de organizacion institucional
    $router->group(['prefix' => 'organizacion-institucional'], function () use ($router) {
        //Mantenimientos
        $router->get('action/{domain_id}', 'AccionController@index');
        $router->get('action/get/{id}', 'AccionController@show');
        $router->post('action/{domain_id}', 'AccionController@store');
        $router->put('action/{domain_id}/{id}', 'AccionController@update');
        $router->delete('action/{domain_id}/{id}', 'AccionController@destroy');
    });

    // ofertas de empleoss
    $router->get('ofertas-empleo', 'OfertasEmpleoController@index');
    $router->post('ofertas-empleo', 'OfertasEmpleoController@store');
    $router->get('ofertas-empleo/{id}', 'OfertasEmpleoController@show');
    $router->put('ofertas-empleo/{id}', 'OfertasEmpleoController@update');
    $router->delete('ofertas-empleo/{id}', 'OfertasEmpleoController@destroy');

    // Rutas para proyectos
    $router->get('proyectos/domain/{domain_id}', 'ProyectosController@index'); // Listar todos los proyectos
    $router->post('proyectos', 'ProyectosController@store'); // Crear un nuevo proyecto
    $router->get('proyectos/{id}', 'ProyectosController@show'); // Mostrar un proyecto específico
    $router->put('proyectos/{id}', 'ProyectosController@update'); // Actualizar un proyecto
    $router->delete('proyectos/{id}', 'ProyectosController@destroy'); // Eliminar un proyecto

    //Rutar para modulos de proyectos
    $router->get('proyectos/{proyectoId}/modulos', 'ProyectosController@listarModulos'); // Listar modulos de un proyecto específico
    $router->post('proyectos/{proyectoId}/modulos', 'ProyectosController@anadirModulo'); // Añadir un modulo a un proyecto
    $router->get('proyectos/{proyectoId}/modulos/{moduloId}', 'ProyectosController@mostrarModulo');
    $router->put('proyectos/{proyectoId}/modulos/{moduloId}', 'ProyectosController@actualizarModulo'); // Actualizar una tarea de un proyecto
    $router->delete('proyectos/{proyectoId}/modulos/{moduloId}', 'ProyectosController@eliminarModulo'); // Eliminar una tarea de un proyecto

    // Rutas para tareas de proyectos
    $router->get('proyectos/{proyectoId}/modulos/{moduloId}/tareas', 'ProyectosController@listarTareas'); // Listar tareas de un proyecto específico
    $router->get('proyectos/{proyectoId}/modulos/{moduloId}/tareas/{tareaId}', 'ProyectosController@mostrarTarea'); // leer una tarea
    $router->post('proyectos/{proyectoId}/modulos/{moduloId}/tareas', 'ProyectosController@anadirTarea'); // Añadir una tarea a un proyecto
    $router->put('proyectos/{proyectoId}/modulos/{moduloId}/tareas/{tareaId}', 'ProyectosController@actualizarTarea'); // Actualizar una tarea de un proyecto
    $router->delete('proyectos/{proyectoId}/modulos/{moduloId}/tareas/{tareaId}', 'ProyectosController@eliminarTarea'); // Eliminar una tarea de un proyecto
    $router->put('proyectos/{proyectoId}/modulos/{moduloId}/tareas/{tareaId}/estado', 'ProyectosController@updateTareaEstado');


    //Candidatos
    $router->get('candidatos/domain/{domain_id}', 'CandidatoController@index');
    $router->post('candidatos', 'CandidatoController@store');
    $router->get('candidatos/{id}', 'CandidatoController@show');
    $router->get('getCiudadByCandidato/{id}', 'CandidatoController@getCiudadByCandidato');
    $router->get('candidatos/user/{id}', 'CandidatoController@showByUser');
    $router->get('candidatos/ciudad/{ciudad_id}', 'CandidatoController@getByCiudad');
    $router->put('candidatos/{id}', 'CandidatoController@update');
    $router->delete('candidatos/{id}', 'CandidatoController@destroy');
    $router->get('candidatos/ciudad/count/{ciudad_id}', 'CandidatoController@countCandidatosByCiudad');
    // Rutas adicionales para datos y filtros
    $router->get('candidatos/filters/data', 'CandidatoController@filtersData');
    $router->get('candidatos/data/create/{domain_id}', 'CandidatoController@dataCreate');

    // Ciudades
    $router->get('ciudades', 'CiudadController@index');
    $router->post('ciudades', 'CiudadController@store');
    $router->get('ciudades/{id}', 'CiudadController@show');
    $router->get('ciudades/domain/{domain_id}', 'CiudadController@listByDomain');
    $router->put('ciudades/{id}', 'CiudadController@update');
    $router->delete('ciudades/{id}', 'CiudadController@destroy');

    // Información Académica Candidato
    $router->get('informacion_academica/domain/{domain_id}', 'InformacionAcademicaCandidatoController@getByDomainId');
    $router->post('informacion_academica', 'InformacionAcademicaCandidatoController@store');
    $router->get('informacion_academica/{id}', 'InformacionAcademicaCandidatoController@show');
    $router->put('informacion_academica/{id}', 'InformacionAcademicaCandidatoController@update');
    $router->delete('informacion_academica/{id}', 'InformacionAcademicaCandidatoController@destroy');
    $router->get('informacion_academica/create/{domain_id}', 'InformacionAcademicaCandidatoController@getDataCreate');

    // Ubigeos
    $router->get('departamentos', 'UbigeoController@departamentos');
    $router->get('departamentos/{departamento_id}/provincias', 'UbigeoController@provincias');
    $router->get('departamentos/{departamento_id}/provincias/{provincia_id}/distritos', 'UbigeoController@distritos');
    // Reuniones
    $router->get('reuniones', 'ReunionController@index');
    $router->post('reuniones', 'ReunionController@store');
    $router->get('reuniones/{id}', 'ReunionController@show');
    $router->put('reuniones/{id}', 'ReunionController@update');
    $router->post('reuniones/{id}/fotos', 'ReunionController@storeFoto');

    //crud tareas
    $router->get('tareas', 'TareasController@index');
    $router->post('tareas', 'TareasController@store');
    $router->get('tareas/{id}', 'TareasController@show');
    $router->put('tareas/{id}', 'TareasController@update');
    $router->delete('tareas/{id}', 'TareasController@destroy');
    $router->get('proyectos/{proyectoId}/tareas', 'TareasController@getByProyecto');
    $router->get('tareas/{proyectoId}/modulos', 'TareasController@getByModuloProyecto');

});
