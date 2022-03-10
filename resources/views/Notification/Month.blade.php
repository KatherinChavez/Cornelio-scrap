<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <title>Reporte mail</title>
</head>

<body style="background-color: #205796!important; margin: auto;">
<!--container-->
<div style="width: 90%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; margin-bottom: 40px;">
    <!--row justify-content-center-->
    <div class="" style=" display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; -ms-flex-pack: center!important; justify-content: center!important;">
        <!--col-md-12-->
        <div style="position: relative; width: 100%; min-height: 1px; padding-right: 15px; padding-left: 15px;">
            <!--card mx-auto mt-5-->
            <div class="" style="margin-left: auto!important;margin-right: auto!important; margin-top: 3rem!important; word-wrap: break-word; background-color: #fff; background-clip: border-box; border: 1px solid rgba(0,0,0,.125); border-radius: .25rem;">
                <!--card-header-->
                <div class="" style="padding: .75rem 1.25rem; margin-bottom: 0; background-color: rgba(0,0,0,.03); border-bottom: 1px solid rgba(0,0,0,.125);"><i class="fa fa-area-chart"></i><strong style="color:#0f3760;"> Reporte Cornelio </strong></div>
                @if( $tipo===1)
                    <img name="Reporte semanal" src="{{ asset('imagen/apps/reporte_semanal.jpeg') }}" alt="Reporte semanal" title="Reporte semanal" style="display:block; margin:auto;">
                @endif
                @if( $tipo===2)
                    <img name="Reporte mensual" src="{{ asset('imagen/apps/reporte_mensual.jpeg') }}" alt="Reporte mensual" title="Reporte mensual" style="display:block; margin:auto; width: 100%">
                @endif

                <!--card-body-->
                <div class="" style="-ms-flex: 1 1 auto; flex: 1 1 auto; padding: 1.25rem; width: 100%">
                    <!--container-fluid-->
                    <div class="" style="width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; margin-left: 10%">
                        <!--row-->
                        <div style="display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
                            <h2 style="padding: 0px; margin: 0px; color: #1D5A93;">Hola,</h2>
                        </div>
                        <div style="display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
                            <p style="color:#0f3760; margin: 5px">En el siguiente Link encontrarás un resumen ejecutivo de las alertas registradas durante el dia {{ $fecha1 }} al {{ $fecha2 }}</p>
                        </div>
                        <div style="display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
                            <a href="{{ $link }}">Ver Reporte en nuestro sitio web</a>
                        </div>
                        <div style="display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
                            <p style="color:#0f3760; margin: 5px">Cualquier consulta adicional, Estoy a la orden</p>
                        </div>
                        <div style="display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: -15px; margin-left: -15px;">
                        </div>
                    </div>
                </div>
                <div class="" style="padding: .75rem 1.25rem; margin-bottom: 0; background-color: rgba(0,0,0,.03); border-bottom: 1px solid rgba(0,0,0,.125);">
                    <p style=" text-align: center; color:#0f3760;">Reporte Generado por <a href="https://goo.gl/4oMRBD" target="_blank">Agencia Digital de Costa Rica </a>- <a href="https://goo.gl/w2Stra" target="_blank">Cornel.io</a> Todos los Derechos Reservados</p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>