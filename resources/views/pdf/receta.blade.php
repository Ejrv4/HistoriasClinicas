<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* CONFIGURACIÓN GLOBAL DE PÁGINA */
        @page { 
            size: a4 portrait; 
            margin: 1.5cm; /* Margen físico real de la hoja */
        }

        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 11px; 
            margin: 0; 
            padding: 0;
            line-height: 1.3;
        }

        /* MARCA DE AGUA (Imagen 3) */
        .watermark {
            position: fixed;
            top: 25%;
            left: 5%;
            width: 90%;
            opacity: 0.07;
            z-index: -1000;
        }

        /* ENCABEZADO CENTRADO */
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
        }
        .doc-name { font-size: 22px; font-weight: bold; color: #1a237e; margin: 0; }
        .doc-specialty { font-size: 12px; color: #1a237e; margin: 4px 0; }
        .doc-codes { 
            font-size: 11px; 
            font-weight: bold; 
            color: #1a237e;
            border-top: 1.5px solid #1a237e; 
            border-bottom: 1.5px solid #1a237e; 
            display: inline-block; 
            padding: 3px 25px;
            margin-top: 8px;
        }

        /* DATOS DEL PACIENTE */
        .patient-box { 
            width: 100%; 
            border-top: 1px solid #444; 
            border-bottom: 1px solid #444; 
            padding: 12px 0;
            margin-bottom: 20px;
        }
        .label { font-weight: bold; text-transform: uppercase; font-size: 10px; }

        /* CUERPO DE LA RECETA */
        .section-title { 
            text-align: center; 
            font-weight: bold; 
            font-size: 13px; 
            margin: 20px 0; 
            text-decoration: underline; 
        }
        .rp-symbol { 
            font-size: 42px; 
            font-weight: bold; 
            font-style: italic; 
            color: #1a237e; 
            margin: 0 0 10px 10px;
        }
        .content-area { min-height: 480px; width: 100%; }

        /* FIRMA CENTRADA (Imagen 1) */
        .signature-section {
            position: fixed;
            bottom: 150px;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .signature-img { 
            width: 220px; 
            display: block; 
            margin: 0 auto -10px auto; 
        }
        .signature-line { 
            width: 280px; 
            border-top: 1px solid #000; 
            margin: 0 auto; 
            padding-top: 5px;
            font-weight: bold;
        }

        /* PIE DE PÁGINA */
        .footer { 
            position: fixed; 
            bottom: 0px; 
            left: 0;
            width: 100%;
            text-align: center; 
            font-size: 10px; 
            color: #333;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .icon { width: 10px; vertical-align: middle; margin-right: 4px; }

        /* SALTOS DE PÁGINA */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@php $secciones = ['PRESCRIPCIÓN MÉDICA', 'INDICACIONES MÉDICAS']; @endphp

@foreach($secciones as $index => $titulo)
    <img src="{{ public_path('img/image3.png') }}" class="watermark">

    <div class="header">
        <h1 class="doc-name">DR. HRISTO ROMÁN VARGAS</h1>
        <p class="doc-specialty">Cirujano Abdominal & Laparoscópico</p>
        <div class="doc-codes">CMP: 84489 | RNE: 52457</div>
    </div>

    <div class="patient-box">
        <table width="100%">
            <tr>
                <td width="70%"><span class="label">Nombre:</span> {{ $paciente }}</td>
                <td align="right"><span class="label">Edad:</span> {{ $edad }} años</td>
            </tr>
            <tr>
                <td><span class="label">DNI:</span> {{ $dni }}</td>
                <td align="right"><span class="label">Fecha:</span> {{ $fecha }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 5px;">
                    <span class="label">Diagnóstico:</span> {{ $diagnostico }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">{{ $titulo }}</div>

    <div class="content-area">
        @if($index === 0) <div class="rp-symbol">Rp.</div> @endif
        
        <table width="100%" cellpadding="6" style="margin-top: 5px;">
            @foreach($recetas as $r)
            <tr>
                <td style="border-bottom: 0.5px solid #ccc;">
                    <strong style="font-size: 12px;">{{ $r->medicamento }} ({{ $r->presentacion }})</strong><br>
                    @if($index === 1)
                        <span style="color: #444;">Tomar {{ $r->dosis }} cada {{ $r->frecuencia }} por {{ $r->duracion }} ({{ $r->via_administracion }})</span>
                    @endif
                </td>
                @if($index === 0)
                <td align="right" style="border-bottom: 0.5px solid #ccc;">
                    <strong style="font-size: 12px;">{{ $r->cantidad_total }} Unid.</strong>
                </td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>

    <div class="signature-section">
        <img src="{{ public_path('img/image1.png') }}" class="signature-img">
        <div class="signature-line">
            Dr. Hristo Román Vargas<br>
            <span style="font-size: 9px; font-weight: normal;">Cirujano Abdominal & Laparoscópico</span>
        </div>
    </div>

    <div class="footer">
        <p style="margin-bottom: 4px;">Jr. José Gálvez 613. Consultorio: 302, Magdalena</p>
        <div>
            <img src="{{ public_path('img/image2.png') }}" class="icon"> 922037667 &nbsp;&nbsp;&nbsp;
            <img src="{{ public_path('img/image4.png') }}" class="icon"> drhristoroman@gmail.com
        </div>
    </div>

    @if($index === 0) <div class="page-break"></div> @endif
@endforeach

</body>
</html>