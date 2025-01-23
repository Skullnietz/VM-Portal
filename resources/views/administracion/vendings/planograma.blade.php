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
<div class="card floating-card" id="draggableCard" style="position: fixed; top: 100px; left: 100px;">
    <div id="cardHeader" class="card-header bg-success">
        <input type="text" id="searchBar" class="form-control" placeholder="Buscar artículos...">
    </div>
    <div class="card-body">
        <div id="searchResults" class="list-group">
            @foreach ($articulos as $articulo)
                <div 
                    class="list-group-item draggable-item"
                    draggable="true" 
                    data-id="{{ $articulo->Id_Articulo }}"
                    data-codigo="{{ $articulo->Txt_Codigo }}"
                    data-descripcion="{{ $articulo->Txt_Descripcion }}">
                    {{ $articulo->Txt_Descripcion }} ({{ $articulo->Txt_Codigo }})
                </div>
            @endforeach
        </div>
    </div>
    <div class="card-footer">
        <label for="transparencyControl">Transparencia:</label>
        <input type="range" id="transparencyControl" min="0" max="100" value="100" class="form-control">
    </div>
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
                                                    <td class="droppable-cell" data-id="{{ $seleccion->Id_Configuracion }}">
                                                    <div class="mb-2">
                                                        <!-- Mensaje para selección vacía, por defecto oculto -->
                                                        <div class="seleccion-vacia" style="display: none;">
                                                            <p class="text-muted">Selección vacía</p>
                                                        </div>

                                                        <!-- Contenido visible cuando hay una selección (imagen, código, descripción, formulario) -->
                                                        <div class="contenido-seleccion">
                                                            <img src="/Images/product.png" 
                                                                    class="img-fluid" 
                                                                    alt="Artículo" 
                                                                    style="max-height: 100px;min-width: 100px;min-height: 100px;max-width: 100px;">
                                                            <p class="text-muted mt-1 TxtCodigo">{{ $seleccion->Txt_Codigo ?? '' }}</p>
                                                            <small class="TxtDescripcion">{{ $seleccion->Txt_Descripcion ?? '' }}</small>

                                                            <form>
                                                                <input type="hidden" class="IdArticulo" value="{{ $seleccion->Id_Articulo ?? '' }}">
                                                                <div class="form-group">
                                                                    <input type="number" class="form-control form-control-sm CantidadMax" value="{{ $seleccion->Cantidad_Max ?? '' }}" placeholder="Máx." disabled>
                                                                </div>
                                                                <div class="form-group">
                                                                    <input type="number" class="form-control form-control-sm CantidadMin" value="{{ $seleccion->Cantidad_Min ?? '' }}" placeholder="Mín." disabled>
                                                                </div>
                                                                <button type="button" class="btn btn-danger btn-sm remove-article-btn">Vaciar</button>
                                                            </form>
                                                        </div>
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
                    <div class="card-footer text-right">
                        <button id="saveChangesBtn" class="btn btn-success">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .floating-card {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 300px;
    }

    .draggable-item {
        cursor: grab;
    }

    .droppable-cell {
        border: 2px dashed transparent;
        transition: border-color 0.2s;
    }

    .droppable-cell.drag-over {
        border-color: #28a745;
    }
</style>
@stop

@section('js')
<script>
// Actualizar la funcionalidad de Drag-and-Drop después de la búsqueda
function setupDragAndDrop() {
    // Asignar el evento dragstart a los elementos de la lista de artículos
    document.querySelectorAll('.draggable-item').forEach(item => {
        item.addEventListener('dragstart', e => {
            // Guardamos la información del artículo cuando comienza el arrastre
            e.dataTransfer.setData('text/plain', JSON.stringify({
                id: e.target.dataset.id,
                codigo: e.target.dataset.codigo,
                descripcion: e.target.dataset.descripcion
            }));

            // Opcional: Puedes añadir un estilo visual para indicar que el elemento se está arrastrando
            e.target.classList.add('dragging');
        });

        item.addEventListener('dragend', e => {
            // Al finalizar el arrastre, eliminamos el estilo visual
            e.target.classList.remove('dragging');
        });
    });

    // Asignar los eventos de dragover, dragleave y drop a las celdas de destino
    document.querySelectorAll('.droppable-cell').forEach(cell => {
        cell.addEventListener('dragover', e => {
            e.preventDefault(); // Necesario para permitir el drop
            cell.classList.add('drag-over'); // Añadimos clase de estilo visual (opcional)
        });

        cell.addEventListener('dragleave', () => {
            cell.classList.remove('drag-over');
        });

        cell.addEventListener('drop', e => {
            e.preventDefault(); // Prevenimos el comportamiento predeterminado (navegar)
            cell.classList.remove('drag-over');

            const data = JSON.parse(e.dataTransfer.getData('text/plain'));

            // Actualizamos los campos en la celda de destino
            const idArticuloElement = cell.querySelector('.IdArticulo');
            const codigoElement = cell.querySelector('.TxtCodigo');
            const descripcionElement = cell.querySelector('.TxtDescripcion');
            const cantidadMaxElement = cell.querySelector('.CantidadMax');
            const cantidadMinElement = cell.querySelector('.CantidadMin');
            const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
            const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

            // Si el id del artículo está vacío, mostramos "Selección vacía"
            if (!data.id) {
                idArticuloElement.value = '';
                codigoElement.textContent = '';
                descripcionElement.textContent = '';
                cantidadMaxElement.value = '';
                cantidadMinElement.value = '';
                
                if (seleccionVaciaElement) {
                    seleccionVaciaElement.style.display = 'block'; // Mostrar mensaje de selección vacía
                }
                
                if (contenidoSeleccionElement) {
                    contenidoSeleccionElement.style.display = 'none'; // Ocultamos contenido
                }

                cantidadMaxElement.disabled = true;
                cantidadMinElement.disabled = true;
            } else {
                idArticuloElement.value = data.id;
                codigoElement.textContent = data.codigo;
                descripcionElement.textContent = data.descripcion;
                cantidadMaxElement.value = 0;
                cantidadMinElement.value = 0;

                if (seleccionVaciaElement) {
                    seleccionVaciaElement.style.display = 'none';
                }
                
                if (contenidoSeleccionElement) {
                    contenidoSeleccionElement.style.display = 'block'; // Mostrar contenido
                }

                cantidadMaxElement.disabled = false;
                cantidadMinElement.disabled = false;
            }

            // Opcional: Realizar una llamada AJAX para guardar el cambio
        });
    });

    // Agregar el evento de "Eliminar" al botón para restaurar la selección vacía
    document.querySelectorAll('.remove-article-btn').forEach(button => {
        button.addEventListener('click', e => {
            const cell = e.target.closest('.droppable-cell');
            const idArticuloElement = cell.querySelector('.IdArticulo');
            const codigoElement = cell.querySelector('.TxtCodigo');
            const descripcionElement = cell.querySelector('.TxtDescripcion');
            const cantidadMaxElement = cell.querySelector('.CantidadMax');
            const cantidadMinElement = cell.querySelector('.CantidadMin');
            const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
            const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

            // Restablecemos la celda a su estado vacío
            idArticuloElement.value = '';
            codigoElement.textContent = '';
            descripcionElement.textContent = '';
            cantidadMaxElement.value = '';
            cantidadMinElement.value = '';

            if (seleccionVaciaElement) {
                seleccionVaciaElement.style.display = 'block';
            }

            if (contenidoSeleccionElement) {
                contenidoSeleccionElement.style.display = 'none';
            }

            cantidadMaxElement.disabled = true;
            cantidadMinElement.disabled = true;
        });
    });
}

// Llamada para verificar las celdas al cargar la página
document.querySelectorAll('.droppable-cell').forEach(cell => {
    const idArticuloElement = cell.querySelector('.IdArticulo');
    const cantidadMaxElement = cell.querySelector('.CantidadMax');
    const cantidadMinElement = cell.querySelector('.CantidadMin');
    const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
    const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

    // Verificar si el Id_Articulo es vacío o nulo al cargar
    if (idArticuloElement && !idArticuloElement.value) {
        if (seleccionVaciaElement) {
            seleccionVaciaElement.style.display = 'block'; // Mostrar "Selección vacía"
        }
        if (contenidoSeleccionElement) {
            contenidoSeleccionElement.style.display = 'none'; // Ocultar la imagen, inputs y botón
        }
        cantidadMaxElement.disabled = true;
        cantidadMinElement.disabled = true;
    } else {
        if (seleccionVaciaElement) {
            seleccionVaciaElement.style.display = 'none'; // Ocultar "Selección vacía"
        }
        if (contenidoSeleccionElement) {
            contenidoSeleccionElement.style.display = 'block'; // Mostrar la imagen, inputs y botón
        }
        cantidadMaxElement.disabled = false;
        cantidadMinElement.disabled = false;
    }
});

// Llamada para verificar las celdas al cargar la página
document.querySelectorAll('.droppable-cell').forEach(cell => {
    const idArticuloElement = cell.querySelector('.IdArticulo');
    const cantidadMaxElement = cell.querySelector('.CantidadMax');
    const cantidadMinElement = cell.querySelector('.CantidadMin');
    const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
    const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

    // Verificar si el Id_Articulo es vacío o nulo al cargar
    if (idArticuloElement && !idArticuloElement.value) {
        if (seleccionVaciaElement) {
            seleccionVaciaElement.style.display = 'block'; // Mostrar "Selección vacía"
        }
        if (contenidoSeleccionElement) {
            contenidoSeleccionElement.style.display = 'none'; // Ocultar la imagen, inputs y botón
        }
        cantidadMaxElement.disabled = true;
        cantidadMinElement.disabled = true;
    } else {
        if (seleccionVaciaElement) {
            seleccionVaciaElement.style.display = 'none'; // Ocultar "Selección vacía"
        }
        if (contenidoSeleccionElement) {
            contenidoSeleccionElement.style.display = 'block'; // Mostrar la imagen, inputs y botón
        }
        cantidadMaxElement.disabled = false;
        cantidadMinElement.disabled = false;
    }
});


// Llamada para verificar las celdas al cargar la página
document.querySelectorAll('.droppable-cell').forEach(cell => {
    const idArticuloElement = cell.querySelector('.IdArticulo');
    const cantidadMaxElement = cell.querySelector('.CantidadMax');
    const cantidadMinElement = cell.querySelector('.CantidadMin');
    const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
    const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

    // Verificar si el Id_Articulo es vacío o nulo al cargar
    if (idArticuloElement && !idArticuloElement.value) {
        if (seleccionVaciaElement) {
            seleccionVaciaElement.style.display = 'block'; // Mostrar "Selección vacía"
        }
        if (contenidoSeleccionElement) {
            contenidoSeleccionElement.style.display = 'none'; // Ocultar la imagen, inputs y botón
        }
        cantidadMaxElement.disabled = true;
        cantidadMinElement.disabled = true;
    } else {
        if (seleccionVaciaElement) {
            seleccionVaciaElement.style.display = 'none'; // Ocultar "Selección vacía"
        }
        if (contenidoSeleccionElement) {
            contenidoSeleccionElement.style.display = 'block'; // Mostrar la imagen, inputs y botón
        }
        cantidadMaxElement.disabled = false;
        cantidadMinElement.disabled = false;
    }
});
// Realizar la búsqueda
document.getElementById('searchBar').addEventListener('input', function () {
    const query = this.value;
    const resultsContainer = document.getElementById('searchResults');

    if (query.length === 0) {
        resultsContainer.innerHTML = ''; // Limpiar si no hay búsqueda
        @foreach ($articulos as $articulo)
            resultsContainer.innerHTML += `
                <div class="list-group-item draggable-item"
                     draggable="true"
                     data-id="{{ $articulo->Id_Articulo }}"
                     data-codigo="{{ $articulo->Txt_Codigo }}"
                     data-descripcion="{{ $articulo->Txt_Descripcion }}">
                    {{ $articulo->Txt_Descripcion }} ({{ $articulo->Txt_Codigo }})
                </div>
            `;
        @endforeach
        setupDragAndDrop(); // Reasignar eventos de drag and drop después de cargar los elementos
        return;
    }

    // Realizar solicitud AJAX con el Id de la Vending
    fetch(`/admin/config/plano/1?search=${query}`)
        .then(response => response.json())
        .then(data => {
            resultsContainer.innerHTML = ''; // Limpiar resultados previos
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'list-group-item draggable-item';
                div.draggable = true;
                div.dataset.id = item.Id_Articulo;
                div.dataset.codigo = item.Txt_Codigo;
                div.dataset.descripcion = item.Txt_Descripcion;
                div.textContent = `${item.Txt_Descripcion} (${item.Txt_Codigo})`;
                resultsContainer.appendChild(div);
            });
            setupDragAndDrop(); // Reasignar eventos de drag and drop después de cargar los elementos
        })
        .catch(error => console.error('Error:', error));
});

setupDragAndDrop(); // Inicializar los eventos de drag and drop al cargar la página

document.getElementById('saveChangesBtn').addEventListener('click', () => {
    const updatedData = [];

    document.querySelectorAll('.droppable-cell').forEach(cell => {
        const idArticulo = cell.querySelector('.IdArticulo')?.value;
        const cantidadMax = cell.querySelector('.CantidadMax')?.value;
        const cantidadMin = cell.querySelector('.CantidadMin')?.value;

        if (idArticulo) {
            updatedData.push({
                idConfiguracion: cell.dataset.id,
                idArticulo: idArticulo,
                cantidadMax: cantidadMax || 0,
                cantidadMin: cantidadMin || 0
            });
        }
    });

    // Enviar datos al servidor
    fetch(`/admin/config/plano/save`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ updatedData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cambios guardados exitosamente.');
        } else {
            alert('Hubo un problema al guardar los cambios.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al guardar los cambios.');
    });
});
</script>
<!-- JavaScript para hacer la tarjeta movible y ajustable en transparencia -->
<script>
    const card = document.getElementById('draggableCard');
    const cardHeader = document.getElementById('cardHeader'); // Selecciona el encabezado
    let offsetX, offsetY;
    let isDragging = false;

    // Función para manejar el inicio del arrastre
    cardHeader.addEventListener('mousedown', (e) => {
        // Detectar el desplazamiento inicial
        offsetX = e.clientX - card.getBoundingClientRect().left;
        offsetY = e.clientY - card.getBoundingClientRect().top;
        isDragging = true;

        // Evitar selección de texto mientras se arrastra
        document.body.style.userSelect = 'none';
    });

    // Función para mover la tarjeta
    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            // Actualizar la posición de la tarjeta en base a la posición del mouse
            card.style.left = e.clientX - offsetX + 'px';
            card.style.top = e.clientY - offsetY + 'px';
        }
    });

    // Función para finalizar el arrastre
    document.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.style.userSelect = '';  // Restaurar la selección de texto
    });

    // Función para ajustar la transparencia de la tarjeta
    const transparencyControl = document.getElementById('transparencyControl');
    transparencyControl.addEventListener('input', () => {
        const transparencyValue = transparencyControl.value;
        card.style.opacity = transparencyValue / 100;  // Convertir el valor del rango a opacidad
    });
</script>
<script>
    function goBack() {
        window.history.back();
    }
</script>
@stop
