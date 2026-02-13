<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planograma - {{ $maquina->Txt_Nombre ?? 'Vending' }}</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            background-color: #fff;
            color: #333;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-logo {
            max-height: 80px;
            width: auto;
        }

        .header-info {
            text-align: right;
        }

        .header-info h1 {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
            color: #000;
        }

        .header-info h2 {
            font-size: 16px;
            font-weight: 600;
            margin: 5px 0 0;
            color: #444;
        }

        .header-info p {
            margin: 2px 0 0;
            font-size: 12px;
            color: #666;
        }

        .charola-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
            background-color: #f8f9fa;
            /* Light grey background for separation */
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #e0e0e0;
        }

        .charola-header {
            background-color: #2c3e50;
            color: #fff;
            padding: 8px 15px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: inline-block;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .products-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-start;
        }

        .product-card {
            flex: 0 0 calc(16.666% - 10px);
            /* Approx 6 items per row */
            background: #fff;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            min-height: 180px;
            /* Ensure consistent height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Adjust for larger wrapping if needed */
        @media print {
            .product-card {
                break-inside: avoid;
            }
        }

        .selection-badge {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #000;
            color: #fff;
            padding: 2px 10px;
            font-weight: bold;
            border-radius: 4px;
            font-size: 14px;
            box-shadow: 0 2px 2px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .product-img {
            height: 90px;
            width: 100%;
            object-fit: contain;
            margin: 15px 0 5px;
        }

        .product-code {
            font-weight: 700;
            font-size: 13px;
            color: #000;
            margin-bottom: 2px;
        }

        .product-desc {
            font-size: 10px;
            color: #555;
            line-height: 1.2;
            height: 25px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin-bottom: 5px;
        }

        .product-meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            border-top: 1px solid #eee;
            padding-top: 5px;
            margin-top: auto;
        }

        .badge-talla {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .empty-card {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-style: italic;
        }

        /* Screen preview styling */
        @media screen {
            .container-fluid {
                max-width: 210mm;
                /* A4 width */
                margin: 20px auto;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
                padding: 40px;
                /* Simulate margins */
            }
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none;
            }

            .container-fluid {
                width: 100%;
                max-width: none;
                padding: 0;
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="fixed-top no-print p-3 text-right">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow">Imprimir</button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg shadow ml-2">Cerrar</button>
    </div>

    <div class="container-fluid">
        <div class="header-container">
            <div>
                <img src="/Images/header_planograma.png" alt="Grupo Urvina" class="header-logo"
                    onerror="this.onerror=null;this.src='';this.outerHTML='<h1 style=\'font-size:36px;color:#004b87;\'>PLANOGRAMA VM</h1>'">
            </div>
            <div class="header-info">
                <h1>{{ $maquina->Txt_Nombre_Planta ?? 'Planta' }}</h1>
                <h2>{{ $maquina->Txt_Nombre ?? 'Vending Machine' }}</h2>
                <p>Serie: <strong>{{ $maquina->Txt_Serie_Maquina ?? '--' }}</strong> | Fecha: {{ date('d/m/Y') }}</p>
            </div>
        </div>

        @foreach ($planograma as $charola => $selecciones)
            <div class="charola-section">
                <div class="charola-header">
                    CHAROLA {{ $charola }}
                </div>
                <div class="products-grid">
                    @foreach ($selecciones as $seleccion)
                        <div class="product-card {{ empty($seleccion->Id_Articulo) ? 'empty-card' : '' }}"
                            style="{{ empty($seleccion->Id_Articulo) ? 'background-color: #f8f8f8; border-style: dashed;' : '' }}">
                            <div class="selection-badge">{{ $seleccion->Seleccion }}</div>

                            @if(!empty($seleccion->Id_Articulo))
                                <img src="/Images/Catalogo/{{ $seleccion->Txt_Codigo ?? '' }}.jpg"
                                    onerror="this.src='/Images/product.png'" alt="Img" class="product-img">

                                <div class="product-code">{{ $seleccion->Txt_Codigo ?? 'S/C' }}</div>
                                <div class="product-desc" title="{{ $seleccion->Txt_Descripcion ?? '' }}">
                                    {{ $seleccion->Txt_Descripcion ?? 'Sin descripción' }}
                                </div>

                                <div class="product-meta">
                                    @if(!empty($seleccion->Talla))
                                        <span class="badge-talla">T: {{ $seleccion->Talla }}</span>
                                    @endif
                                    <strong>Max: {{ $seleccion->Cantidad_Max ?? 0 }}</strong>
                                </div>
                            @else
                                <span>VACÍO</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>