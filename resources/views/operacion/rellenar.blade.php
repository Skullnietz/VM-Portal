@extends('adminlte::page')

@section('title', __('Planograma'))

@section('content_header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="row">
        <div class="col-md-9 col-9">
            <h4>
                <a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>
                &nbsp;&nbsp;&nbsp;{{ __('Rellenado VM') }}
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
</div>


<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Planograma</h5>
                </div>
                <div class="card-body">
                    @foreach ($planograma as $charola => $selecciones)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h2>Charola {{ $charola }}</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead class="thead-dark">
                                            <tr>
                                                @for ($i = 0; $i <= 9; $i++)
                                                    <th>Selección {{ $charola }}{{ $i }}</th>
                                                @endfor
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selecciones->chunk(10) as $chunk)
                                                <tr>
                                                    @foreach ($chunk as $seleccion)
                                                        <td class="droppable-cell"
                                                            data-id="{{ $seleccion->Id_Configuracion ?? '' }}"
                                                            data-charola="{{ $seleccion->Num_Charola ?? 'N/A' }}"
                                                            data-seleccion="{{ $seleccion->Seleccion ?? 'N/A' }}">
                                                            <div class="mb-2">
                                                                @if(empty($seleccion->Id_Articulo))
                                                                    <!-- Mostrar la leyenda de selección vacía -->
                                                                    <div class="seleccion-vacia">
                                                                        <p class="text-muted">Selección vacía</p>
                                                                    </div>
                                                                @else
                                                                    <!-- Contenido visible cuando hay un artículo -->
                                                                    <div class="contenido-seleccion">
                                                                        <div class="cantidad-max-container">
                                                                            <small class="text-muted">Máximo:
                                                                                {{ $seleccion->Cantidad_Max ?? 0 }}</small>
                                                                        </div>

                                                                        <div class="articulo-container">
                                                                            <img src="/Images/product.png" class="img-fluid mt-2 mb-2"
                                                                                alt="Artículo"
                                                                                style="max-height: 100px; min-width: 100px; min-height: 100px; max-width: 100px;">
                                                                            <p class="text-muted TxtCodigo">
                                                                                {{ $seleccion->Txt_Codigo ?? '' }}
                                                                            </p>
                                                                            <small class="TxtDescripcion"
                                                                                title="{{ $seleccion->Txt_Descripcion ?? '' }}">{{ \Illuminate\Support\Str::limit($seleccion->Txt_Descripcion ?? '', 40) }}</small>
                                                                            @if(!empty($seleccion->Talla))
                                                                                <div class="mt-1">
                                                                                    <span class="badge badge-pill talla-badge"
                                                                                        data-talla="{{ $seleccion->Talla }}">{{ $seleccion->Talla }}</span>
                                                                                </div>
                                                                            @endif
                                                                        </div>

                                                                        <div class="stock-container">
                                                                            <form>
                                                                                <input type="hidden" class="IdArticulo"
                                                                                    value="{{ $seleccion->Id_Articulo }}">
                                                                                <div class="form-group">
                                                                                    <label for="Stock">Stock</label>
                                                                                    <input type="number"
                                                                                        class="form-control form-control-sm Stock"
                                                                                        value="{{ $seleccion->Stock ?? '' }}"
                                                                                        placeholder="Stock"
                                                                                        min="{{ $seleccion->Cantidad_Min ?? 0 }}"
                                                                                        max="{{ $seleccion->Cantidad_Max ?? 0 }}"
                                                                                        value="{{ $seleccion->Stock ?? '' }}">
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endforeach
                                                    @for ($i = $chunk->count(); $i < 10; $i++)
                                                        <td class="droppable-cell"></td>
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
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
        // --- Helper Functions ---

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
        document.querySelectorAll('.Stock').forEach(input => {
            input.addEventListener('input', updateStockColors);
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

        // --- Save Logic ---

        let pendingStockUpdate = [];

        const saveChangesBtn = document.getElementById('saveChangesBtn');
        if (saveChangesBtn) {
            saveChangesBtn.addEventListener('click', function () {
                const cells = document.querySelectorAll('.droppable-cell');
                pendingStockUpdate = []; // Reset pending update

                try {
                    const summaryData = [];
                    let hasChanges = false;

                    Array.from(cells).forEach(cell => {
                        const id = cell.getAttribute('data-id');
                        const charola = cell.getAttribute('data-charola');
                        const seleccion = cell.getAttribute('data-seleccion');
                        const stockInput = cell.querySelector('.Stock');
                        const articuloDesc = cell.querySelector('.TxtDescripcion') ? cell.querySelector('.TxtDescripcion').innerText : 'N/A';
                        const tallaBadge = cell.querySelector('.talla-badge');
                        const talla = tallaBadge ? tallaBadge.innerText : '';

                        if (!stockInput) return;

                        let stockValue = parseInt(stockInput.value) || 0;

                        // Get initial value from the attribute rendered by server
                        const initialStock = parseInt(stockInput.getAttribute('value')) || 0;
                        const maxStock = parseInt(stockInput.getAttribute('max')) || 0;

                        // Validation
                        if (stockValue < 0) {
                            throw new Error(`El stock para la charola ${charola}, selección ${seleccion} no puede ser negativo.`);
                        }
                        if (stockValue > maxStock) {
                            stockValue = maxStock;
                            stockInput.value = maxStock;
                        }

                        if (stockValue !== initialStock) {
                            hasChanges = true;
                            summaryData.push({
                                Seleccion: seleccion,
                                Articulo: articuloDesc,
                                Talla: talla,
                                Cantidad_Anterior: initialStock,
                                Cantidad_Rellenada: stockValue - initialStock,
                                Cantidad_Nueva: stockValue
                            });
                        }

                        pendingStockUpdate.push({ id, stock: stockValue });
                    });

                    if (!hasChanges) {
                        alert('No hay cambios para guardar.');
                        return;
                    }

                    // Populate Modal
                    const thead = document.querySelector('#summaryContent table thead tr');
                    if (thead) {
                        thead.innerHTML = `
                            <th>Selección</th>
                            <th>Artículo</th>
                            <th>Talla</th>
                            <th>Anterior</th>
                            <th>Rellenado</th>
                            <th>Nuevo</th>
                        `;
                    }

                    const tbody = document.getElementById('summaryTableBody');
                    if (tbody) {
                        tbody.innerHTML = '';
                        summaryData.forEach(item => {
                            const row = `<tr>
                                <td>${item.Seleccion}</td>
                                <td>${item.Articulo}</td>
                                <td>${item.Talla}</td>
                                <td>${item.Cantidad_Anterior}</td>
                                <td>${item.Cantidad_Rellenada}</td>
                                <td>${item.Cantidad_Nueva}</td>
                            </tr>`;
                            tbody.innerHTML += row;
                        });
                    }

                    // Populate Aggregated Table
                    const aggregatedTableBody = document.getElementById('aggregatedTableBody');
                    if (aggregatedTableBody) {
                        aggregatedTableBody.innerHTML = '';
                        const aggregatedData = {};

                        summaryData.forEach(item => {
                            const key = `${item.Articulo}|${item.Talla}`;
                            if (!aggregatedData[key]) {
                                aggregatedData[key] = {
                                    Articulo: item.Articulo,
                                    Talla: item.Talla,
                                    Total_Rellenado: 0
                                };
                            }
                            aggregatedData[key].Total_Rellenado += item.Cantidad_Rellenada;
                        });

                        Object.values(aggregatedData).forEach(item => {
                            if (item.Total_Rellenado > 0) {
                                const row = `<tr>
                                    <td>${item.Articulo}</td>
                                    <td>${item.Talla}</td>
                                    <td>${item.Total_Rellenado}</td>
                                </tr>`;
                                aggregatedTableBody.innerHTML += row;
                            }
                        });
                    }

                    // Re-enable the confirm button in case it was disabled from a previous attempt
                    const confirmBtn = document.getElementById('confirmSaveBtn');
                    if (confirmBtn) {
                        confirmBtn.disabled = false;
                        confirmBtn.innerText = 'Confirmar Guardado';
                    }

                    $('#summaryModal').modal('show');

                } catch (error) {
                    alert(error.message);
                    console.error(error);
                }
            });
        }

        // New Confirm Button Listener
        const confirmSaveBtn = document.getElementById('confirmSaveBtn');
        if (confirmSaveBtn) {
            confirmSaveBtn.addEventListener('click', function () {
                const locale = '{{ app()->getLocale() }}';

                // Disable button to prevent double submit
                this.disabled = true;
                this.innerText = 'Guardando...';

                fetch(`/${locale}/operador/update-stock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ updatedStock: pendingStockUpdate })
                }).then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert('Cambios guardados exitosamente.');
                            window.location.reload(); // Reload to update UI and reset state
                        } else {
                            alert('Hubo un error al guardar los cambios');
                            // Re-enable button
                            this.disabled = false;
                            this.innerText = 'Confirmar Guardado';
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Error de red o servidor.');
                        this.disabled = false;
                        this.innerText = 'Confirmar Guardado';
                    });
            });
        }
    });

    function printSummary() {
        const printContent = document.getElementById('summaryContent').innerHTML;
        const originalContent = document.body.innerHTML;

        document.body.innerHTML = `
            <h1>Resumen de Relleno</h1>
            ${printContent}
        `;

        window.print();

        document.body.innerHTML = originalContent;
        window.location.reload();
    }

    function goBack() {
        window.history.back();
    }
</script>
@stop