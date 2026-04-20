<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
/* ======================================================
CARGA DE FUENTES LOCALES (PARA DOMPDF) - SIN CAMBIOS
====================================================== */
@font-face { font-family: 'Playfair Display SC'; src: url("{{ public_path('fonts/PlayfairDisplaySC-Bold.ttf') }}") format('truetype'); font-weight: 700; font-style: normal; }
@font-face { font-family: 'Playfair Display'; src: url("{{ public_path('fonts/PlayfairDisplay-VariableFont_wght.ttf') }}") format('truetype'); font-weight: 400; font-style: normal; }
@font-face { font-family: 'Inter'; src: url("{{ public_path('fonts/Inter_28pt-Regular.ttf') }}") format('truetype'); }
@font-face { font-family: 'Montserrat'; src: url("{{ public_path('fonts/Montserrat-Bold.ttf') }}") format('truetype'); font-weight: 900; }
@font-face { font-family: 'Imperial Script'; src: url("{{ public_path('fonts/ImperialScript-Regular.ttf') }}") format('truetype'); }

@page { 
    size: a4 portrait; 
    margin: 0.8cm 1.5cm 1.5cm 1.5cm;
}

body { 
    font-family: 'Inter', sans-serif; 
    font-size: 16px; 
    margin: 0; 
    padding: 0;
    line-height: 1.1;
}

.watermark { position: fixed; top: 25%; left: 5%; width: 90%; opacity: 0.07; z-index: -1000; }
.logo { position: fixed; top: -25px; left: 0; width: 142px; height: auto; z-index: -1; }
.header { text-align: center; margin-bottom: 10px; }
.doc-name { font-family: 'Playfair Display SC', serif; font-weight: 700; font-size: 28px; color: #1a237e; margin: -8px 0; }
.doc-specialty { font-family: 'Playfair Display', serif; font-weight: 400; font-size: 18px; color: #1a237e; margin: -5px 0; }
.doc-codes { font-family: 'Inter', sans-serif; font-size: 14px; color: #1a237e; border-top: 1.5px solid #1a237e; border-bottom: 1.5px solid #1a237e; display: inline-block; padding: 1px 15px; margin-top: 8px; }
.patient-box { width: 100%; border-top: 1px solid #444; border-bottom: 1px solid #444; padding: 3px 0; margin: 5px; }
.label { font-family: 'Inter', sans-serif; font-weight: bold; text-transform: uppercase; font-size: 14px; }
.section-title { font-family: 'Montserrat', sans-serif; font-weight: 900; font-size: 18px; text-align: center; margin: 5px 0; }
.rp-symbol { font-family: 'Imperial Script'; font-size: 42px; color: #1a237e; margin: -20px 0 -5px 0; }
.content-area { min-height: 480px; width: 100%; }

.signature-section { position: fixed; bottom: 50px; left: 0; width: 100%; text-align: center; }
.signature-img { width: 220px; display: block; margin: 20px auto -30px auto; }
.signature-line { width: 280px; border-top: 1px solid #000; margin: 0 auto; padding-top: 3px; font-weight: bold; line-height: 0.8; }
.footer { position: fixed; bottom: -30px; left: 0; width: 100%; text-align: center; font-size: 12px; color: #333; border-top: 1px solid #eee; padding-top: 0px; }
.icon { width: 12px; vertical-align: middle; margin-right: 4px; }
.page-break { page-break-after: always; }
</style>
</head>

<body>

{{-- Elementos fijos que se repiten en todas las hojas --}}
<img src="{{ public_path('img/image3.png') }}" class="logo">
<img src="{{ public_path('img/image3.png') }}" class="watermark">

<div class="signature-section">
    <img src="{{ public_path('img/image1.png') }}" class="signature-img">
    <div class="signature-line">
        <span style="font-family: 'Playfair Display SC', serif; font-size: 18px;">Dr. Hristo Román Vargas</span><br>
        <span style="font-family: 'Playfair Display', serif; font-size: 14px;">Cirujano Abdominal & Laparoscópico</span><br>
        <span style="font-family: 'Inter'; font-size: 12px">CMP: 84489 | RNE: 52457</span>
    </div>
</div>

<div class="footer">
    <p style="margin-bottom:4px;">Jr. José Gálvez 613. Consultorio: 302, Magdalena</p>
    <div>
        <img src="{{ public_path('img/image2.png') }}" class="icon"> 922037667
        &nbsp;&nbsp;&nbsp;
        <img src="{{ public_path('img/image4.png') }}" class="icon"> drhristoroman@gmail.com
    </div>
</div>

@php
    $secciones = ['PRESCRIPCIÓN MÉDICA', 'INDICACIONES MÉDICAS'];
    $recetas_chunk = collect($recetas)->chunk(8); 
@endphp

@foreach($recetas_chunk as $grupo)
    @foreach($secciones as $index => $titulo)
        
        <div class="header">
            <h1 class="doc-name">Dr. Hristo Román Vargas</h1>
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
                    <td colspan="2"><span class="label">Diagnóstico:</span> {{ $diagnostico }}</td>
                </tr>
            </table>
        </div>

        <div class="section-title">{{ $titulo }}</div>

        <div class="content-area">
            @if($index === 0)
                <div class="rp-symbol">Rp.</div>
            @endif

            <table width="100%" cellpadding="6" style="margin-top:5px;">
                @foreach($grupo as $r)
                    <tr>
                        <td style="border-bottom:0.5px solid #ccc;">
                            <strong style="font-size:16px;">{{ $r->medicamento }} ({{ $r->presentacion }})</strong>
                            <br>
                            @if($index === 1)
                                <span style="color:#444; font-size:15px;">
                                    Tomar {{ $r->dosis }} cada {{ $r->frecuencia }} por {{ $r->duracion }} ({{ $r->via_administracion }})
                                </span>
                            @endif
                        </td>
                        @if($index === 0)
                            <td align="right" style="border-bottom:0.5px solid #ccc;">
                                <strong style="font-size:16px;">{{ $r->cantidad_total }} Unid.</strong>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>

        {{-- Solo hace salto de página si NO es la última sección de la última tanda --}}
        @if (!$loop->last || !$loop->parent->last)
            <div class="page-break"></div>
        @endif

    @endforeach
@endforeach

</body>
</html>