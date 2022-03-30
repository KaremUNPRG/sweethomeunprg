@extends('web.layout.appWeb')
@section('Content')
  <style media="screen">
    .title-mensaje{
      text-align: center;
      padding: 10px 0px;
      line-height: 22px;
      width: 100%;
      cursor: pointer;
      font-weight: 600;
    }
    .title-mensaje::after{
      content: "";
      width: 64px;
      background: rgb(255, 85, 0);
      height: 2px;
      display: block;
      margin: 0px auto;
    }
  </style>
      <!-- Start Proerty header  -->
  @php
    $imagen = strpos($Propiedad->imagen, 'base64');
    if($Propiedad->imagen == null){
      $Propiedad->imagen = 'frontend\asset\img\camera.jpg';
    }
  @endphp
  <section id="aa-property-header" style="background:none;height:500px">
    <img src="{{$imagen === false?asset($Propiedad->imagen):$Propiedad->imagen}}" style="position: absolute;top: 0;bottom: 0;left: 0;right: 0;width: 100%;height: 100%;z-index: -11;" alt="">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-property-header-inner">
            <h2>DETALLE DE LA PROPIEDAD</h2>
            <ol class="breadcrumb">
            <li><a href="{{route('web.inicio')}}">INICIO</a></li>
          </ol>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End Proerty header  -->
  <!-- Start Properties  -->
  <section id="aa-properties">
    <div class="container">
      <div class="row">
        <div class="col-md-8">
          <div class="aa-properties-content">
            <!-- Start properties content body -->
            <div class="aa-properties-details">
             <div class="aa-properties-details-img">
               @if($Galeria[0]->ubicacion != null)
                 @foreach ($Galeria as $item)
                  <img src="{{asset($item->ubicacion)}}" alt="img">
                 @endforeach
               @else
                <img src="{{asset('frontend\asset\img\camera.jpg')}}" alt="img">
               @endif
             </div>
             <div class="aa-properties-info">
               <h2>{{$Propiedad->DescripcionPropiedad}}</h2>
               <span class="aa-price">
                {{($Propiedad->moneda == 'SOLES')?'S/ ':(($Propiedad->moneda == 'DOLAR')?'$ ':'E ')}} {{$Propiedad->precio}}
              </span>
               <p>{{$Propiedad->informacion}}</p>
               <h4>Caracteristicas de la Propiedad</h4>
               <ul>
                 @foreach ($Caracteristicas as $item)
                  <li>{{$item->descripcion}}</li>
                 @endforeach
               </ul>
               @empty(!$Propiedad->video)
                <h4>Video de la propiedad</h4>
                <iframe width="100%" height="480" src="{{$Propiedad->video}}" frameborder="0" allowfullscreen></iframe>
               @endempty
               <h4>Ubicación de la Propiedad</h4>
               <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6851.201919469417!2d{{$Propiedad->longitud}}!3d{{$Propiedad->latitud}}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x888bdb60cc49c571%3A0x40451ca6baf275c7!2s36008+AL-77%2C+Talladega%2C+AL+35160%2C+USA!5e0!3m2!1sbn!2sbd!4v1460452919256" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
             </div>
             <!-- Properties social share -->
            </div>
          </div>
        </div>
        <!-- Start properties sidebar -->
        <div class="col-md-4">
          <div class="aa-contact-form" style="display: flex;flex-wrap: wrap;justify-content: center;background:#e7eef0">
            <h4 class="title-mensaje">Mensaje</h4>
            <form id="formProceso" class="form_solicitar row col-xs-12" action="{{route('app.solicitudproceso')}}" method="post">
              @csrf
              <input type="hidden" name="itmPublicacion" id="itmPublicacion" value="{{$Propiedad->id_propiedad}}">
              <div class="col-md-12" style="padding-bottom:1rem;">
                <div class="input-container-main">
                  <!-- <label for="input-email" class="label-form-main" style="">Documento</label> -->
                  <select class="select-form-main" name="imtTurno">
                    <option value="Dia">Día</option>
                    <option value="Noche">Noche</option>
                  </select>
                  <!-- <input id="input-email" type="text" name="email" inputmode="text" class="input-form-main"  value="" placeholder=""> -->
                </div>
              </div>
              <div class="col-md-12" style="padding-bottom:1rem;">
                <div class="input-container-main">
                  <input id="itmVisita" type="date" name="itmVisita" autocomplete="off" class="input-form-main"  value="" placeholder="">
                  <label for="itmVisita" class="label-form-main" style="background:#fff">Dia Visita</label>
                </div>
              </div>
              <div class="col-md-12" style="padding-bottom:1rem;">
                <div class="input-container-main">
                  <input id="itmCorreo" type="email" name="itmCorreo" autocomplete="off" class="input-form-main"  value="" placeholder="">
                  <label for="itmCorreo" class="label-form-main" style="">Correo</label>
                </div>
              </div>
              <div class="col-md-12" style="padding-bottom:1rem;">
                <div class="input-container-main">
                  <input id="itmTelefono" type="text" name="itmTelefono" autocomplete="off" class="input-form-main"  value="" placeholder="">
                  <label for="itmTelefono" class="label-form-main" style="">Telefono</label>
                </div>
              </div>
              <div class="col-md-12 px-3 pt-5 pb-3" style="padding-bottom:1rem;">
                <div class="input-container-main">
                  <textarea style="height: 140px!important;" name="itmInformacion" id="itmInformacion" placeholder="Mensaje" class="input-form-main" autocomplete="off" rows="8" cols="6"></textarea>
                  <!-- <label for="itmInformacion" class="label-form-main" style="">Descripción</label> -->
                </div>
              </div>
              <div class="col-md-12" style="margin-bottom:50px">
                <div class="d-flex" style="display:flex;justify-content:center;">
                  <div class="col-md-12">
                    <button type="button" id="btnContactar" class="btn" name="button">Contactar</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- / Properties  -->
@endsection

@section('script')
  <!-- <script src="{{asset('backend/asset/lib/gentelella/vendors/jquery/dist/jquery.min.js')}}"></script> -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script type="text/javascript">
    function EjecutarProceso() {
      $.ajax({
          type: "post",
          url: $('#formProceso').attr('action'),
          data: $('#formProceso').serialize(),
          dataType: "json",
          headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (response) {
            if (response.Status == 'Success') {
              $('#formProceso')[0].reset();
              swal({
                title: 'Resultado',
                text: response.Mesagge,
                icon: "success",
              });
            }else{
              swal({
                title: response.Meta.Error_Type,
                text: response.Meta.Error_Message,
                icon: "error",
              });
            }
          }
      });
    }
    $('#btnContactar').click(function () {
      EjecutarProceso();
    })
  </script>
@endsection
