<?php

namespace App\Http\Controllers\Negocio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProcesoController extends Controller
{
  //config('app.debug')
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

    public function RegistrarArrendador(REQUEST $request)
    {
      // dd(self::$Response['Meta']['Debug']);
      try {
        DB::beginTransaction();
        $newUsuario = DB::table('pt_usuario')->insertGetId([
            'correo'    => $request->input('itmCorreo'),
            'password'  => Hash::make($request->input('itmDocumento')),
            'tipo'      => 'PRO'
          ]);
        $newPropietario = DB::table('pt_propietario')->insertGetId([
          'nombre'        => $request->input('itmNombre'),
          'apellido'      => $request->input('itmApellido'),
          'razon_social'  => $request->input('itmRazonSocial'),
          'documento'     => $request->input('itmDocumento'),
          'tipo_documento'=> $request->input('itmTipoDocumento'),
          'id_usuario'    => $newUsuario,
          'telefono'      => $request->input('itmTelefono'),
          'correo'        => $request->input('itmCorreo'),
          'direccion'     => $request->input('itmDireccion')
        ]);
        DB::commit();
        self::$Response['Mesagge'] = 'El Creo Correctament su usuario';
        self::$Response['Data'] = $newUsuario;
      } catch (\Exception $e) {
        DB::rollBack();
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
}