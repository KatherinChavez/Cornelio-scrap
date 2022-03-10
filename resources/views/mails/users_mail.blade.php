<body style="margin: 0; padding: 0;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td style="padding: 10px 0 30px 0;">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border: 1px solid #a0b9c8; border-collapse: collapse;">
                <tr>
                    <td align="center" bgcolor="#2b698e" style="padding: 40px 0 30px 0; color: #ffffff; font-size: 15px; font-weight: bold; font-family: Arial, sans-serif; ">
                        <img src="{{ asset('img/logo.png')}}" alt="Cornel.io" style="display: block;"/>
                        <p align="right">{{ $data['created_at'] }}</p>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 20px;">
                                    <h2 align="center">Saludos, </h2>
                                    <h4 align="center">Sus datos son los siguientes:</h4>
                                    <p>Nombre: {{ $data['name']}} {{ $data['last_name']}} {{ $data['sec_last_name']}}</p>
                                    <p>Correo electrónico: {{ $data['email']}}</p>
                                    <p>Contraseña generada aleatóricamente: {{ $data['password']}}</p>
                                </td>
                            </tr>
                            <tr>
{{--                                <td style="color: #153643; font-family: Arial, sans-serif; font-size: 16px; line-height: 20px;">--}}
{{--                                    <h3>Acabás de registrarte con el correo:  {{ $data['contact_email'] }}.</h3><br>--}}

{{--                                    A manera de recordatorio, <b>el número de celular</b> que registraste es el siguiente: <b>{{ $data['phone'] }}</b><br>--}}
{{--                                    Además, <b>el código de verificación</b> que te debe llegar según tu número telefónico es el:  <b>{{ $data['codigo'] }}</b>, este código es para que verifiqués tu número de celular registrado, ya que tus paquetes comprados en línea están relacionados a ese número.<br>--}}
{{--                                    Para realizarlo, debés dirigirte a la SV, en el módulo de <b>"Teléfonos".</b><br><br>--}}
{{--                                    Para nosotros es muy importante que conozcas cuál es tu código único de Correos de CR, es el siguiente: <b>{{ $data['ccr_cod'] }}</b><br>--}}
{{--                                </td>--}}
                            </tr>
                        </table>
                    </td>
                </tr>
{{--                <tr>--}}
{{--                    <td bgcolor="#39D03E" style="padding: 30px 30px 30px 30px;">--}}
{{--                        <table border="0" cellpadding="0" cellspacing="0" width="100%">--}}
{{--                            <tr>--}}
{{--                                <td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="75%">--}}
{{--                                    © {{date('Y')}} Sucursal Virtual. Todos los derechos reservados.--}}
{{--                                </td>--}}
{{--                                <td align="right" width="25%">--}}
{{--                                    <table border="0" cellpadding="0" cellspacing="0">--}}
{{--                                        <tr>--}}
{{--                                            <td style="font-family: Arial, sans-serif; font-size: 12px; font-weight: bold;">--}}
{{--                                                <a href="https://www.correos.go.cr/" style="color: #ffffff;">--}}
{{--                                                    <img src="{{ asset('img/logo_ccr_blanco.png')}}" alt="CCR" width="45%" height="45%" style="display: block;" align="right" border="0" />--}}
{{--                                                </a>--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}
{{--                                    </table>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        </table>--}}
{{--                    </td>--}}
{{--                </tr>--}}
            </table>
        </td>
    </tr>
</table>
</body>
