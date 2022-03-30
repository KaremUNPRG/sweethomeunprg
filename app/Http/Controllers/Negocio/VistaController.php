<?php

namespace App\Http\Controllers\Negocio;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VistaController extends Controller
{

    public function Inicio(REQUEST $request)
    {
      // $request->session()->flush();
        $NuevaPropiedades = DB::table('pt_propiedad')->where('estado','!=','ANU')->get();
        return view('web.inicio')
                    ->with('NuevaPropiedad',$NuevaPropiedades);
    }

    public function DetallePropiedad(REQUEST $request,$codigo)
    {
        $DetallePropiedad = DB::table('pt_propiedad as pr')
                            ->select(DB::raw('*,pr.descripcion as DescripcionPropiedad'))
                            ->leftjoin('pt_galeria as ga','ga.id_propiedad','=','pr.id_propiedad')
                            ->leftjoin('pt_caracteristica as ca','ca.id_propiedad','=','pr.id_propiedad')
                            ->where('pr.id_propiedad','=',$codigo)
                            ->get();
        // dd($DetallePropiedad);
        $Caracteristicar = array();
        $Galeria = array();
        foreach ($DetallePropiedad as $key => $value) {
            $posCaract = array_search($value->id_caracteristica,array_column($Caracteristicar,'id_caracteristica'));
            $posGaleria = array_search($value->id_galeria,array_column($Galeria,'id_galeria'));
            if($posCaract === false){
                array_push($Caracteristicar,$value);
            }
            if($posGaleria === false){
                array_push($Galeria,$value);
            }
        }
        $DetallePropiedad = count($DetallePropiedad) > 0 ? $DetallePropiedad[0] : null;
        // dd($Galeria);
        // dd($DetallePropiedad);
        return view('web.detallePropiedad')
                    ->with('Caracteristicas',$Caracteristicar)
                    ->with('Galeria',$Galeria)
                    ->with('Propiedad',$DetallePropiedad);
    }

    public function Contactar(REQUEST $request)
    {
        return view('web.contactar');
    }

    public function RegistroArrendador(REQUEST $request)
    {
        return view('web.arrendador.registro');
    }
}
