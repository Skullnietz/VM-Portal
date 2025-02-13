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
<div id="floatingActions" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; background-color: white; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); padding: 15px;">
    <button id="saveChangesBtn" class="btn btn-success mb-2" style="width: 100%;">Guardar Cambios</button>
    <button id="fillMaxFloatingBtn" class="btn btn-primary" style="width: 100%;">Rellenar Máximos</button>
</div>


<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Configuración del Planograma</h5>
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
                                                                        <small class="text-muted">Máximo: {{ $seleccion->Cantidad_Max ?? 0 }}</small>
                                                                    </div>

                                                                    <div class="articulo-container">
                                                                        <img src="/Images/product.png" 
                                                                            class="img-fluid mt-2 mb-2" 
                                                                            alt="Artículo" 
                                                                            style="max-height: 100px; min-width: 100px; min-height: 100px; max-width: 100px;">
                                                                        <p class="text-muted TxtCodigo">{{ $seleccion->Txt_Codigo ?? '' }}</p>
                                                                        <small class="TxtDescripcion">{{ $seleccion->Txt_Descripcion ?? '' }}</small>
                                                                    </div>

                                                                    <div class="stock-container">
                                                                        <form>
                                                                            <input type="hidden" class="IdArticulo" value="{{ $seleccion->Id_Articulo }}">
                                                                            <div class="form-group">
                                                                                <label for="Stock">Stock</label>
                                                                                <input type="number" 
                                                                                    class="form-control form-control-sm Stock" 
                                                                                    value="{{ $seleccion->Stock ?? '' }}" 
                                                                                    placeholder="Stock" 
                                                                                    min="{{ $seleccion->Cantidad_Min ?? 0 }}" 
                                                                                    max="{{ $seleccion->Cantidad_Max ?? 0 }}">
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
@stop

@section('css')
<style>
.cantidad-max-container {
    background-color: #f0f0f0; /* Fondo gris claro */
    color:red;
    padding: 5px;
    border-radius: 5px;
    text-align: center;
    margin-bottom: 10px;
    font-weight: bold;
    font-size: 19px;
}

.stock-container {
    background-color: #d0d0d0; /* Fondo gris claro */
    padding: 10px;
    border-radius: 5px;
    text-align: center;
}

.articulo-container {
    text-align: center;
    height: 270px; /* Ajusta según sea necesario */
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

        // Actualizar colores al cargar la página
        updateStockColors();

        // Escuchar cambios en los inputs de stock
        document.querySelectorAll('.Stock').forEach(input => {
            input.addEventListener('input', updateStockColors);
        });

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
    } else {
        console.error('El botón "Rellenar Máximos" flotante no existe en el DOM.');
    }
});
</script>
<script defer>
  document.getElementById('saveChangesBtn').addEventListener('click', function () {
    const cells = document.querySelectorAll('.droppable-cell');
    try {
        const updatedStock = Array.from(cells).map(cell => {
            const id = cell.getAttribute('data-id');
            const charola = cell.getAttribute('data-charola');
            const seleccion = cell.getAttribute('data-seleccion');
            const stockInput = cell.querySelector('.Stock');

            if (!stockInput) {
                return null; // Ignorar celdas sin input
            }

            const stockValue = parseInt(stockInput.value) || 0;
            const minStock = parseInt(stockInput.getAttribute('min')) || 0;
            const maxStock = parseInt(stockInput.getAttribute('max')) || 0;

            // Validar el valor del stock
            if (stockValue < minStock || stockValue > maxStock) {
                throw new Error(
                    `El stock para la charola ${charola}, selección ${seleccion} debe estar entre ${minStock} y ${maxStock}.`
                );
            }

            return { id, stock: stockValue };
        }).filter(item => item !== null); // Filtrar elementos nulos

        // Enviar datos actualizados al servidor
        fetch('/update-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ updatedStock })
        }).then(response => {
            if (response.ok) {
                alert('Cambios guardados exitosamente');
            } else {
                alert('Hubo un error al guardar los cambios');
            }
        }).catch(error => console.error('Error:', error));

    } catch (error) {
        alert(error.message);
    }
});


</script>
<script defer>
    document.addEventListener('DOMContentLoaded', function () {
        const saveChangesFloatingBtn = document.getElementById('saveChangesFloatingBtn');
        

        if (saveChangesFloatingBtn) {
            saveChangesFloatingBtn.addEventListener('click', function () {
                const saveChangesBtn = document.getElementById('saveChangesBtn');
                if (saveChangesBtn) {
                    saveChangesBtn.click(); // Simula un clic en el botón principal
                } else {
                    console.error('El botón principal "Guardar Cambios" no existe en el DOM.');
                }
            });
        } else {
            console.error('El botón "Guardar Cambios" flotante no existe en el DOM.');
        }
    });
</script>
<script>
    function goBack() {
        window.history.back();
    }
</script>
@stop
