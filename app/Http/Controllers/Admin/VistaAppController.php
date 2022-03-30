<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Auth;
use Illuminate\Support\Facades\Storage;

class VistaAppController extends Controller
{
  public static $Response = [
    'Meta'    => [
            'Debug'         => '',
            'Error_Type'    => '',
            'Code'          => -1,
            'Error_Message' => ''
          ],
    'Data'    => [],
    'Mesagge' => '',
    'Status'  => 'Success'
  ];
  public static $Code   = 200;
  public static $Header = [];

  public function __construct()
  {
    self::$Response['Meta']['Debug'] = config('app.debug');
  }

  public function App(REQUEST $request)
  {
      dd($request);
  }

  public function RealizarPublicacion(REQUEST $request)
  {
    $Departamento = DB::table('pt_ubigeo_departamento')->get();
    $Provincia = DB::table('pt_ubigeo_provincia')->get();
    $Distrito = DB::table('pt_ubigeo_distrito')->get();

    return view('admin.publicacion')
          ->with('Departamento',$Departamento)
          ->with('Provincia',$Provincia)
          ->with('Distrito',$Distrito);
  }

  public function RespuestaSolicitudVista(REQUEST $request)
  {
    return view('admin.respuesta');
  }

  public function VisitaSolicitud(REQUEST $request)
  {
    return view('admin.visitaSolicitud');
  }

  public function ProcesoPublicacion(REQUEST $request)
  {
    // dd(json_decode($request->itmCaracteristicas));
    try {
      if ($request->itmAccion == 'Nuevo') {
        try {
          DB::beginTransaction();
          $arrayCaracteristica = [];
          $coordenada = json_decode($request->itmCoordenada);
          $caracteristica = json_decode($request->itmCaracteristicas);
          $idPropiedad = DB::table('pt_propiedad')->insertGetId([
            'descripcion'         =>  $request->itmDescripcion,
            // 'publicacion_fecha'   =>  date('YYYY-mm-dd'),
            'departamento'        =>  $request->itmDepartamento,
            'provincia'           =>  $request->itmProvincia,
            'distrito'            =>  $request->itmDistrito,
            'direccion'           =>  $request->itmDireccion,
            'longitud'            =>  $coordenada->Latitud,
            'latitud'             =>  $coordenada->Longitud,
            'informacion'         =>  $request->itmInformacion,
            'estado'              =>  'NUE',
            'id_propietario'      =>  Auth::Info('pt_propietario','id_usuario')->id_propietario,
            'precio'              =>  $request->itmPrecio,
            'moneda'              =>  $request->itmMoneda,
            'imagen'              =>  $request->itmImagenPrincipal
          ]);
          foreach ($caracteristica as $key => $item) {
            array_push($arrayCaracteristica,[
              'descripcion'   => $item->descripcion,
              'id_propiedad'  => $idPropiedad
            ]);
          }
          if (!empty($arrayCaracteristica)) {
            $idCaracteristica = DB::table('pt_caracteristica')->insert($arrayCaracteristica);
          }
          self::$Response['Mesagge'] = 'Se Creo Correctament la publicación';
          self::$Response['Data'] = $idPropiedad;
          DB::commit();
        } catch (\Exception $e) {
          DB::rollBack();
          self::$Response['Status'] = 'Error';
          self::$Response['Meta']['Error_Type'] = 'Server';
          self::$Response['Meta']['Error_Message'] = 'Error al registrar';
          self::$Response['Meta']['Code'] = 500;
          if (self::$Response['Meta']['Debug']) {
            self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
          }
        }

      }
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,200,[]);
  }

  public function ListarPublicacion(REQUEST $request)
  {
    try {
      $datos = DB::table('pt_propiedad as pr')
                  ->select(DB::raw('*,
                  (SELECT dp.nombre FROM pt_ubigeo_departamento as dp WHERE dp.id_departamento = pr.departamento) Departamento,
                  (SELECT pro.nombre FROM pt_ubigeo_provincia as pro WHERE pro.id_provincia = pr.provincia) Provincia,
                  (SELECT dis.nombre FROM pt_ubigeo_distrito as dis WHERE dis.id_distrito = pr.distrito) Distrito'))
                  ->where('pr.id_propietario','=',Auth::Info('pt_propietario','id_usuario')->id_propietario)
                  ->get();
      self::$Response['Mesagge'] = 'Datos obtenidos';
      self::$Response['Data'] = $datos;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function ProcesoSolicitud(REQUEST $request)
  {
    try {
      $proceso = DB::table('pt_solicitud_visita')->insert([
        'turno'         => $request->imtTurno,
        'dia'           => $request->itmVisita,
        'correo'        => $request->itmCorreo,
        'telefono'      => $request->itmTelefono,
        'mensaje'       => $request->itmInformacion,
        'id_propiedad'  => $request->itmPublicacion,
        'realizo_solicitud'=>Auth::Info('pt_propietario','id_usuario')->id_propietario
      ]);
      self::$Response['Mesagge'] = 'Se registro Correctamente la Solicitud';
      self::$Response['Data'] = $proceso;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function ListarVisitaSolicitud(REQUEST $request)
  {
    try {
      $datos = DB::table('pt_solicitud_visita as sov')
                  ->select(DB::raw('
                  *,
                  (SELECT re.id_respuesta FROM pt_respuesta re WHERE re.id_solicitud = sov.id_solicitud LIMIT 1) as Respuesta,
                  (SELECT co.id_contrato FROM pt_contrato co WHERE co.id_solicitud = sov.id_solicitud LIMIT 1) as Contrato '))
                  ->join('pt_propiedad as pro','pro.id_propiedad','=','sov.id_propiedad')
                  ->where('pro.id_propietario','=',Auth::Info('pt_propietario','id_usuario')->id_propietario)
                  ->get();
      self::$Response['Mesagge'] = 'Datos obtenidos';
      self::$Response['Data'] = $datos;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function SubirGaleria(REQUEST $request)
  {
    dd($request->get());
    try {
      $proceso = DB::table('pt_galeria')->insert([
        'ubicacion'         => $request->imtTurno,
        'id_propiedad'           => $request->itmVisita
      ]);
      self::$Response['Mesagge'] = 'Se registro Correctamente la Solicitud';
      self::$Response['Data'] = $proceso;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function RespuestaSolicitud(REQUEST $request)
  {
    try {
      // dd($request->itmAceptar);
      $proceso = DB::table('pt_respuesta')->insert([
        'aceptar'           => ($request->itmAceptar == 'SI')?$request->itmAceptar:'NO',
        'mensaje'           => $request->itmRespuesta,
        'id_solicitud'      => $request->itmSolicitud
      ]);
      self::$Response['Mesagge'] = 'Se registro Correctamente la Respuesta';
      self::$Response['Data'] = $proceso;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function ListarRespuesta(REQUEST $request)
  {
    try {
      $datos = DB::table('pt_respuesta as re')
                  ->select(DB::raw('*,re.mensaje MesajeRespuesta,re.registrado RegistroRespuesta'))
                  ->join('pt_solicitud_visita as sv','re.id_solicitud','=','sv.id_solicitud')
                  ->leftjoin('pt_contrato as co','co.id_solicitud','=','sv.id_solicitud')
                  ->where('sv.realizo_solicitud','=',Auth::Info('pt_propietario','id_usuario')->id_propietario)
                  ->get();
      self::$Response['Mesagge'] = 'Datos obtenidos';
      self::$Response['Data'] = $datos;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function GenerarContrato(REQUEST $request)
  {
    try {
      $datosSolicitud = DB::select('SELECT CONCAT(po.nombre," ",po.apellido) NombreArrendador,
      po.tipo_documento TipoDocArrendador,po.documento DocArrendador, po.direccion DirecArrendador,
  		CONCAT(pp.nombre," ",pp.apellido) NombreArrendatario,pp.tipo_documento TipoDocArrendatario,
  		pp.documento DocArrendatario, pp.direccion DirecArrendatario,
		CONCAT((SELECT dp.nombre FROM pt_ubigeo_departamento as dp WHERE dp.id_departamento = pr.departamento)," / ",
            (SELECT pro.nombre FROM pt_ubigeo_provincia as pro WHERE pro.id_provincia = pr.provincia)," / ",
            (SELECT dis.nombre FROM pt_ubigeo_distrito as dis WHERE dis.id_distrito = pr.distrito)," : ",pr.direccion)
            UbicacionPropiedad,sv.id_solicitud IdSolicitud
      FROM pt_solicitud_visita sv
			INNER JOIN pt_propiedad pr ON sv.id_propiedad = pr.id_propiedad
			INNER JOIN pt_propietario po ON pr.id_propietario = po.id_propietario
			INNER JOIN pt_propietario pp ON pp.id_propietario = sv.realizo_solicitud
			WHERE sv.id_solicitud= ?',[$request->itmSolicitud]);
      // dd($datosSolicitud);
      // dd($request->input());
      $dateDifference = abs(strtotime($request->itmFechaFin) - strtotime($request->itmFechaInicio));
      $years  = floor($dateDifference / (365 * 60 * 60 * 24));
      $months = floor(($dateDifference - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
      $days   = floor(($dateDifference - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 *24) / (60 * 60 * 24));
      // dd(explode('-',$request->itmFechaInicio));
      $data = [
          'FechaInicio' => explode('-',$request->itmFechaInicio),
          'FechaFin'    => explode('-',$request->itmFechaFin),
          'DatosSolicitud'=> (object)$datosSolicitud[0],
          'InfoPropiedad'=> $request->itmInfo,
          'Duracion'  => [$days,$months,$years]
      ];
      $pdf = \PDF::loadView('admin/pdf/contratoAlquiler', $data);
      $nombre = 'Contrato_'.str_pad($request->itmSolicitud,8,'0',STR_PAD_LEFT).'.pdf';
      // $folder = "contrato/".date('d-m-y')."/";
      // \App\Storage::Mkdir($folder);
      // $ubicacion = \App\Storage::put($nombre,$pdf->output(),$folder);
      //$pdfStream = $pdf->stream($nombre);
      Storage::disk('public')->put($nombre, $pdf->output());
      $url = Storage::disk('public')->url($nombre);
      $idContrato = DB::table('pt_contrato')
                            ->insert([
                              'fecha_inicio' => $request->itmFechaInicio,
                              'fecha_fin'   => $request->itmFechaFin,
                              'url_contrato_sin' => $url,
                              'info_propiedad'   => $request->itmInfo,
                              'id_solicitud'    => $request->itmSolicitud
                            ]);
      // dd($url);
      return redirect($url);
      // return $pdf->download('Contrato.pdf');


      // $idContrato = DB::table('pt_contrato')->insertGetId([]);
    } catch (\Exception $e) {
      dd($e);
    }

  }

  public function EnviarNotificacionAlquiler(REQUEST $request)
  {
    try {
      $datos = DB::table('pt_contrato')
                ->where('id_contrato','=',$request->itmContrato)
                ->update([
                  'notificacion' => 'SI'
                ]);
      self::$Response['Mesagge'] = 'Notificación enviada';
      self::$Response['Data'] = $datos;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }

  public function AceptarAlquiler(REQUEST $request)
  {
    try {
      $DatosInsertar = DB::table('pt_solicitud_visita as sov')
                  ->select(DB::raw('*,pro.id_propietario as Propietario,sov.realizo_solicitud as Inquilino'))
                  ->join('pt_propiedad as pro','pro.id_propiedad','=','sov.id_propiedad')
                  ->join('pt_contrato as co','co.id_solicitud','=','sov.id_solicitud')
                  ->where('co.id_contrato','=',$request->itmContrato)
                  ->get();
      // dd($DatosInsertar);
      $idAlquiler = DB::table('pt_alquiler')->insertGetId([
        'id_propietario' => $DatosInsertar[0]->Propietario,
        'fecha_inicio'  => $DatosInsertar[0]->fecha_inicio,
        'fecha_fin'     => $DatosInsertar[0]->fecha_fin,
        'inquilino'     => $DatosInsertar[0]->Inquilino,
        'id_propiedad'  => $DatosInsertar[0]->id_propiedad,
      ]);
      $datos = DB::table('pt_contrato')
                ->where('id_contrato','=',$request->itmContrato)
                ->update([
                  'firmado' => 'SI',
                  'id_alquiler' => $idAlquiler
                ]);
      $datos = DB::table('pt_propiedad')
                ->where('id_propiedad','=',$DatosInsertar[0]->id_propiedad)
                ->update([
                  'estado' => 'ALQ'
                ]);
      self::$Response['Mesagge'] = 'El Alquiler se proceso correctamente';
      self::$Response['Data'] = $datos;
    } catch (\Exception $e) {
      self::$Response['Status'] = 'Error';
      self::$Response['Meta']['Error_Type'] = 'Server';
      self::$Response['Meta']['Error_Message'] = 'Error en el servicio';
      self::$Response['Meta']['Code'] = 500;
      if (self::$Response['Meta']['Debug']) {
        self::$Response['Meta']['Error_Tecnico'] = $e->getMessage();
      }
    }

    return response()->json(self::$Response,self::$Code,self::$Header);
  }
}
