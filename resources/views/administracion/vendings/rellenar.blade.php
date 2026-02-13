@extends('adminlte::page')

@section('title', __('Planograma'))

@section('content_header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="row">
        <div class="col-md-9 col-9">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;{{ __('Planograma') }}
            </h4>
        </div>
        <div class="col-md-3 col-3 ml-auto"></div>
    </div>
</div>
@stop

@section('content')
<div id="floatingActions"
    style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; background-color: white; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); padding: 15px;">
    <button id="saveChangesBtn" class="btn btn-success mb-2" style="width: 100%;">Guardar Cambios</button>
    <button id="fillMaxFloatingBtn" class="btn btn-primary" style="width: 100%;">Rellenar Máximos</button>
    <a href="{{ route('ArellenarPrint', ['id' => request()->route('id')]) }}" target="_blank" class="btn btn-info mt-2"
        style="width: 100%;">
        <i class="fas fa-print"></i> Imprimir Planograma
    </a>
</div>


<div class="container-fluid"> <!-- Changed to container-fluid for more width -->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Configuración del Planograma</h5>
                </div>
                <div class="card-body">
                    @foreach ($planograma as $charola => $selecciones)
                            <div class="card mb-3"
                                style="{{ $loop->iteration % 2 == 0 ? 'background-color: #f8f9fa;' : 'background-color: #e9ecef;' }}">
                                <div class="card-header">
                                    <h2>Charola {{ $charola }}</h2>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex flex-nowrap overflow-auto">
                                        @foreach ($selecciones as $seleccion)
                                            @if(!empty($seleccion->Id_Articulo))
                                                <div class="droppable-cell m-1 border rounded p-2" style="flex: 0 0 auto;"
                                                    data-id="{{ $seleccion->Id_Configuracion ?? '' }}"
                                                    data-charola="{{ $seleccion->Num_Charola ?? 'N/A' }}"
                                                    data-seleccion="{{ $seleccion->Seleccion ?? 'N/A' }}">
                                                    <div class="mb-2">
                                                        <!-- Contenido visible cuando hay un artículo -->
                                                        <div class="contenido-seleccion">
                                                            <div class="articulo-container">
                                                                <img src="/Images/Catalogo/{{ $seleccion->Txt_Codigo ?? '' }}.jpg"
                                                                    onerror="this.src='/Images/product.png'" alt="Imagen Artículo"
                                                                    class="img-fluid mb-2 ImgArticulo" style="max-height: 100px;">
                                                                <p class="mb-1 font-weight-bold">{{ $seleccion->Txt_Codigo ?? '' }}</p>
                                                                <p class="small text-muted">{{ $seleccion->Txt_Descripcion ?? '' }}</p>
                                                                @if(!empty($seleccion->Talla))
                                                                    <div class="mt-1">
                                                                        <span class="badge badge-pill talla-badge"
                                                                            data-talla="{{ $seleccion->Talla }}">Talla:
                                                                            {{ $seleccion->Talla }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="stock-container">
                                                            <form>
                                                                <input type="hidden" class="IdArticulo"
                                                                    value="{{ $seleccion->Id_Articulo }}">
                                                                <div class="form-group">
                                                                    <div class="text-center mb-2">
                                                                        <span class="badge badge-warning p-2"
                                                                            style="font-size: 0.9em;">Max:
                                                                            {{ $seleccion->Cantidad_Max ?? 0 }}</span>
                                                                    </div>
                                                                    <label class="d-block text-center small text-muted mb-1">Stock
                                                                        Actual</label>
                                                                    <div class="input-group input-group-sm">
                                                                        <div class="input-group-prepend">
                                                                            <button class="btn btn-outline-danger btn-decrement"
                                                                                type="button">
                                                                                <i class="fas fa-minus"></i>
                                                                            </button>
                                                                        </div>
                                                                        <input type="number" class="form-control text-center Stock"
                                                                            value="{{ $seleccion->Stock ?? 0 }}"
                                                                            data-initial-stock="{{ $seleccion->Stock ?? 0 }}"
                                                                            placeholder="0" min="0"
                                                                            max="{{ $seleccion->Cantidad_Max ?? 0 }}">
                                                                        <div class="input-group-append">
                                                                            <button class="btn btn-outline-success btn-increment"
                                                                                type="button">
                                                                                <i class="fas fa-plus"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 p-1 bg-dark text-white text-center font-weight-bold rounded">
                                                        {{ $seleccion->Seleccion }}
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

            </div>
        </div>
    </div>
</div>
</div>
<!-- Modal de Resumen -->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel">Resumen de Relleno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="summaryContent">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <!-- Headers will be populated by JS -->
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody">
                            <!-- Summary data will be populated by JS -->
                        </tbody>
                    </table>
                </div>

                <hr>
                <h5>Totales por Artículo y Talla</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Talla</th>
                                <th>Total Rellenado</th>
                            </tr>
                        </thead>
                        <tbody id="aggregatedTableBody">
                            <!-- Aggregated data will be populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="printSummary()">Imprimir</button>
                <button type="button" class="btn btn-success" id="confirmSaveBtn">Confirmar Guardado</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .cantidad-max-container {
        background-color: #f0f0f0;
        /* Fondo gris claro */
        color: red;
        padding: 5px;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 19px;
    }

    .stock-container {
        background-color: #d0d0d0;
        /* Fondo gris claro */
        padding: 10px;
        border-radius: 5px;
        text-align: center;
    }

    .articulo-container {
        text-align: center;
        height: 270px;
        /* Ajusta según sea necesario */
        margin-bottom: 10px;
    }

    .droppable-cell {
        min-width: 180px;
        max-width: 180px;
        background-color: #fff;
    }

    floatingActions {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        padding: 15px;
        width: 200px;
    }

    #floatingActions button {
        margin-bottom: 10px;
    }
</style>


@stop

@section('js')

<script defer>
    document.addEventListener('DOMContentLoaded', function () {
        const updateStockColors = () => {
            const stockContainers = document.querySelectorAll('.stock-container');

            stockContainers.forEach(container => {
                const stockInput = container.querySelector('.Stock');
                const stockValue = parseInt(stockInput.value || 0);
                const minStock = parseInt(stockInput.getAttribute('min') || 0);
                const maxStock = parseInt(stockInput.getAttribute('max') || 0);
                const nearMinStock = minStock + Math.floor((maxStock - minStock) * 0.2); // Cerca del mínimo (20% adicional)

                // Resetear colores
                container.style.backgroundColor = '#f0f0f0';

                if (stockValue <= minStock) {
                    container.style.backgroundColor = 'rgba(255, 99, 71, 0.5)'; // Rojo claro
                } else if (stockValue > minStock && stockValue <= nearMinStock) {
                    container.style.backgroundColor = 'rgba(255, 255, 102, 0.5)'; // Amarillo claro
                } else if (stockValue >= maxStock) {
                    container.style.backgroundColor = 'rgba(144, 238, 144, 0.5)'; // Verde claro
                }
            });
        };

        // Function to generate a color from a string
        function stringToColor(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            let color = '#';
            for (let i = 0; i < 3; i++) {
                let value = (hash >> (i * 8)) & 0xFF;
                // Ensure the color is not too dark or too light for readability
                // Mix with white (255, 255, 255) to make it pastel/lighter
                value = Math.floor((value + 255) / 2);
                color += ('00' + value.toString(16)).substr(-2);
            }
            return color;
        }

        // --- Initialization ---

        // Actualizar colores al cargar la página
        updateStockColors();

        // Escuchar cambios en los inputs de stock
        // Escuchar cambios en los inputs de stock
        document.querySelectorAll('.Stock').forEach(input => {
            input.addEventListener('input', function () {
                let val = parseInt(this.value);
                const max = parseInt(this.getAttribute('max')) || 9999;

                if (isNaN(val) || val < 0) {
                    this.value = 0;
                } else if (val > max) {
                    this.value = max;
                }
                updateStockColors();
            });
            input.addEventListener('blur', function () {
                if (this.value === '') {
                    this.value = 0;
                    updateStockColors();
                }
            });
        });

        // Event delegation for +/- buttons
        document.addEventListener('click', function (e) {
            const btnDecrement = e.target.closest('.btn-decrement');
            const btnIncrement = e.target.closest('.btn-increment');

            if (btnDecrement) {
                const input = btnDecrement.closest('.input-group').querySelector('.Stock');
                if (input) {
                    let value = parseInt(input.value) || 0;
                    if (value > 0) {
                        input.value = value - 1;
                        input.dispatchEvent(new Event('input'));
                    }
                }
            }

            if (btnIncrement) {
                const input = btnIncrement.closest('.input-group').querySelector('.Stock');
                if (input) {
                    let value = parseInt(input.value) || 0;
                    const max = parseInt(input.getAttribute('max')) || 9999;
                    if (value < max) {
                        input.value = value + 1;
                        input.dispatchEvent(new Event('input'));
                    }
                }
            }
        });

        // Colorear badges de talla
        const badges = document.querySelectorAll('.talla-badge');
        badges.forEach(badge => {
            const talla = badge.getAttribute('data-talla');
            if (talla) {
                const bgColor = stringToColor(talla);
                badge.style.backgroundColor = bgColor;
                badge.style.color = '#000'; // Ensure text is readable (black on pastel)
                badge.style.fontSize = '0.9em';
                badge.style.padding = '5px 10px';
            }
        });

        // --- Event Listeners ---

        const fillMaxFloatingBtn = document.getElementById('fillMaxFloatingBtn');
        if (fillMaxFloatingBtn) {
            fillMaxFloatingBtn.addEventListener('click', function () {
                const confirmFill = confirm('¿Estás seguro de que deseas ajustar todas las selecciones a su cantidad máxima?');
                if (confirmFill) {
                    const stockInputs = document.querySelectorAll('.Stock');
                    stockInputs.forEach(input => {
                        const maxStock = parseInt(input.getAttribute('max'));
                        input.value = maxStock; // Ajusta cada input al valor máximo permitido
                    });
                    updateStockColors(); // Actualiza los colores después de rellenar
                    alert('Todas las selecciones se han ajustado a su cantidad máxima.');
                } else {
                    alert('Operación cancelada.');
                }
            });
        }

        // --- Save Changes Logic ---

        document.getElementById('saveChangesBtn').addEventListener('click', function () {
            const cells = document.querySelectorAll('.droppable-cell');
            const summaryTableBody = document.getElementById('summaryTableBody');
            const aggregatedTableBody = document.getElementById('aggregatedTableBody');
            summaryTableBody.innerHTML = '';
            aggregatedTableBody.innerHTML = '';

            let hasChanges = false;
            const aggregatedData = {};

            cells.forEach(cell => {
                const charola = cell.getAttribute('data-charola');
                const seleccion = cell.getAttribute('data-seleccion');
                const stockInput = cell.querySelector('.Stock');
                const articuloContainer = cell.querySelector('.articulo-container');
                const codigo = articuloContainer.querySelector('.font-weight-bold').innerText;
                const tallaBadge = articuloContainer.querySelector('.talla-badge');
                const talla = tallaBadge ? tallaBadge.innerText.replace('Talla: ', '') : 'N/A';

                if (stockInput) {
                    let currentStock = parseInt(stockInput.value) || 0;
                    if (currentStock < 0) currentStock = 0; // Ensure no negative stock
                    const initialStock = parseInt(stockInput.getAttribute('data-initial-stock')) || 0;
                    const diff = currentStock - initialStock;

                    if (diff !== 0) {
                        hasChanges = true;

                        const diffDisplay = diff > 0 ? `+${diff}` : `${diff}`;
                        const diffClass = diff > 0 ? 'text-success' : 'text-danger';

                        // Add to summary table
                        const row = `<tr>
                            <td>${charola}</td>
                            <td>${seleccion}</td>
                            <td>${codigo}</td>
                            <td>${talla}</td>
                            <td>${initialStock}</td>
                            <td>${currentStock}</td>
                            <td class="${diffClass}">${diffDisplay}</td>
                        </tr>`;
                        summaryTableBody.innerHTML += row;

                        // Aggregate data
                        const key = `${codigo}-${talla}`;
                        if (!aggregatedData[key]) {
                            aggregatedData[key] = {
                                codigo: codigo,
                                talla: talla,
                                totalRefill: 0
                            };
                        }
                        aggregatedData[key].totalRefill += diff;
                    }
                }
            });

            // Populate aggregated table
            for (const key in aggregatedData) {
                const item = aggregatedData[key];
                const totalDisplay = item.totalRefill > 0 ? `+${item.totalRefill}` : `${item.totalRefill}`;
                const totalClass = item.totalRefill > 0 ? 'text-success' : (item.totalRefill < 0 ? 'text-danger' : '');

                const row = `<tr>
                    <td>${item.codigo}</td>
                    <td>${item.talla}</td>
                    <td class="${totalClass}">${totalDisplay}</td>
                </tr>`;
                aggregatedTableBody.innerHTML += row;
            }

            if (hasChanges) {
                // Add headers dynamically if needed or ensure they exist in HTML
                const summaryHeaderRow = document.querySelector('#summaryModal thead tr');
                if (summaryHeaderRow.children.length === 0) {
                    summaryHeaderRow.innerHTML = `
                        <th>Charola</th>
                        <th>Selección</th>
                        <th>Artículo</th>
                        <th>Talla</th>
                        <th>Stock Inicial</th>
                        <th>Stock Final</th>
                        <th>Diferencia</th>
                    `;
                }
                $('#summaryModal').modal('show');
            } else {
                alert('No hay cambios para guardar.');
            }
        });

        document.getElementById('confirmSaveBtn').addEventListener('click', function () {
            const cells = document.querySelectorAll('.droppable-cell');
            const updatedStock = [];

            cells.forEach(cell => {
                const id = cell.getAttribute('data-id');
                const stockInput = cell.querySelector('.Stock');

                if (stockInput) {
                    const stockValue = parseInt(stockInput.value) || 0;
                    const initialStock = parseInt(stockInput.getAttribute('data-initial-stock')) || 0;

                    // Only add to updatedStock if the value has changed
                    if (stockValue !== initialStock) {
                        updatedStock.push({ id: id, stock: stockValue });
                    }
                }
            });

            if (updatedStock.length === 0) {
                alert('No hay cambios para guardar.');
                $('#summaryModal').modal('hide');
                return;
            }

            fetch('/update-stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ updatedStock })
            }).then(response => {
                if (response.ok) {
                    $('#summaryModal').modal('hide');
                    alert('Cambios guardados exitosamente');
                    // Update initial stock to current stock to prevent double counting if saved again without reload
                    cells.forEach(cell => {
                        const stockInput = cell.querySelector('.Stock');
                        if (stockInput) {
                            stockInput.setAttribute('data-initial-stock', stockInput.value);
                        }
                    });
                } else {
                    alert('Hubo un error al guardar los cambios');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al guardar los cambios');
            });
        });
    });

    function goBack() {
        window.history.back();
    }

    function printSummary() {
        var printContents = document.getElementById('summaryContent').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;
        // Re-attach event listeners or reload page to restore functionality
        location.reload();
    }
</script>
@stop