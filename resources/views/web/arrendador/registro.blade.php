@extends('web.layout.appWeb')

@section('Content')

  <!-- Start Proerty header  -->

  <section id="aa-property-header">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="aa-property-header-inner">
            <h2>Crear una Cuenta</h2>
            <ol class="breadcrumb">
            <li><a href="./">Inicio</a></li>
            <li class="active">Registro</li>
          </ol>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- End Proerty header  -->

 <section id="aa-contact">
   <div class="container">
     <div class="row">
       <div class="col-md-12">
          <div class="aa-contact-area" style="background-color: #f3f7f8;">
            <div class="aa-contact-bottom">
              <div class="aa-title">
                <h2>Registro</h2>
                <span></span>
              </div>
              <div class="aa-contact-form" style="display: flex;flex-wrap: wrap;justify-content: center;">
                <form id="formRegistro" class="form_registro row col-xs-6" action="{{route('web.registrar.arrendador')}}" method="post">
                  @csrf
                  <div class="col-md-6">
                    <div class="input-container-main">
                      <!-- <label for="input-email" class="label-form-main" style="">Documento</label> -->
                      <select class="select-form-main" name="itmTipoDocumento">
                        <option value="DNI">DNI</option>
                        <option value="RUC">RUC</option>
                      </select>
                      <!-- <input id="input-email" type="text" name="email" inputmode="text" class="input-form-main"  value="" placeholder=""> -->
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-container-main">
                      <input id="itmDocumento" type="text" name="itmDocumento" autocomplete="off" class="input-form-main"  value="" placeholder="">
                      <label for="input-email" class="label-form-main" style="">Documento</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-container-main">
                      <input id="itmNombre" type="text" name="itmNombre" autocomplete="off" class="input-form-main"  value="" placeholder="">
                      <label for="input-email" class="label-form-main" style="">Nombres</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-container-main">
                      <input id="itmApellido" type="text" name="itmApellido" autocomplete="off" class="input-form-main"  value="" placeholder="">
                      <label for="input-email" class="label-form-main" style="">Apellidos</label>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="input-container-main">
                      <input id="itmRazonSocial"  type="text" name="itmRazonSocial" autocomplete="off" class="input-form-main"  value="" placeholder="">
                      <label for="input-email" class="label-form-main" style="">Razón Social</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-container-main">
                      <input id="itmCorreo" type="text" name="itmCorreo" autocomplete="off" class="input-form-main"  value="" placeholder="">
                      <label for="input-email" class="label-form-main" style="">Correo</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="input-container-main">
                      <input id="itmTelefono" type="text" name="itmTelefono" autocomplete="off" class="input-form-main"  value="" placeholder="">
                      <label for="input-email" class="label-form-main" style="">Teléfono</label>
                    </div>
                  </div>
                  <div class="col-md-12" style="margin-bottom:50px">
                    <div class="d-flex" style="display:flex;justify-content:center;">
                      <div class="col-md-6">
                        <button type="button" id="btnRegistrar" class="btn" name="button">Registrar</button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
       </div>
     </div>
   </div>
 </section>
@endsection
@section('script')
  <script>
    $('#btnRegistrar').click(function () {
      $.ajax({
            url: "{{ url('/registrar/arrendador') }}",
            method: 'post',
            dataType: "JSON",
            data: $('#formRegistro').serialize(),
            success: function(result){
                $('#formRegistro')[0].reset();
               console.log(result);
            }
         });
    });
  </script>
@endsection
