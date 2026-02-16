<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Etiquetas - {{ $maquina->Txt_Nombre }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page-header {
            text-align: left;
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Container for the pair to ensure exact 7cm width */
        .pair-container {
            display: flex;
            width: 7cm !important;
            min-width: 7cm !important;
            max-width: 7cm !important;
            height: 1.2cm !important;
            min-height: 1.2cm !important;
            max-height: 1.2cm !important;
            border: 0.5px solid black;
            margin: 1px;
            /* 2px gap via margin */
            page-break-inside: avoid;
            overflow: hidden;
            flex-shrink: 0;
        }

        .lbl-num {
            width: 2cm;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            font-size: 14pt;
            font-weight: bold;
            border-right: 0.5px solid black;
        }

        .lbl-art {
            width: 5cm;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10pt;
            font-weight: bold;
            overflow: hidden;
            white-space: nowrap;
        }


        /* Generated grid wrapper */
        .grid-wrapper {
            display: flex;
            flex-wrap: wrap;
            /* gap handled by margins on items for better print compat */
            justify-content: flex-start;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none;
            }

            .lbl-art {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Ensure A4 size and Landscape */
            @page {
                size: A4 landscape;
                margin: 0.5cm;
            }
        }
    </style>
</head>

<body>
    @php
        // Dynamic Pastel Palette (20+ colors)
        $colors = [
            '#D7BDE2',
            '#AED6F1',
            '#A9DFBF',
            '#F0C27B',
            '#F9E79F',
            '#E5E7E9',
            '#F5B7B1',
            '#D4E157',
            '#76D7C4',
            '#F7DC6F',
            '#85C1E9',
            '#D2B4DE',
            '#F1948A',
            '#BB8FCE',
            '#82E0AA',
            '#F0B27A',
            '#E59866',
            '#FBEEE6',
            '#E8DAEF',
            '#D4EFDF',
            '#FEF9E7',
            '#FDEBD0'
        ];

        // Assign colors to articles
        $articleColors = [];
        $colorIndex = 0;

        foreach ($planograma as $item) {
            if (!isset($articleColors[$item->Id_Articulo])) {
                $articleColors[$item->Id_Articulo] = $colors[$colorIndex % count($colors)];
                $colorIndex++;
            }
        }
    @endphp

    <div class="fixed-top no-print p-3"
        style="position: fixed; top: 0; right: 0; background: #eee; padding: 10px; border: 1px solid #ccc;">
        <button onclick="window.print()"
            style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px;">Imprimir</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; font-size: 16px; cursor: pointer; background: #6c757d; color: white; border: none; border-radius: 4px; margin-left: 5px;">Cerrar</button>
    </div>

    <div class="page-header">
        {{ $maquina->Txt_Nombre }} - {{ $maquina->Txt_Nombre_Planta }}
    </div>

    <div class="grid-wrapper">
        @foreach ($planograma as $item)
            @php
                $bgColor = $articleColors[$item->Id_Articulo];
                $name = $item->Nombre_Etiqueta ?? $item->Txt_Descripcion; // Fallback
                $label = $name . ($item->Talla ? ' ' . $item->Talla : '');
            @endphp
            <div class="pair-container">
                <div class="lbl-num">{{ $item->Seleccion }}</div>
                <div class="lbl-art" style="background-color: {{ $bgColor }};">
                    {{ Str::limit($label, 20, '') }}
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>