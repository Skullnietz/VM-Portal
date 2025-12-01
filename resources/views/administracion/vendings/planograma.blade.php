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
                <div class="list-group-item draggable-item" draggable="true" data-id="{{ $articulo->Id_Articulo }}"
                    data-codigo="{{ $articulo->Txt_Codigo }}" data-descripcion="{{ $articulo->Txt_Descripcion }}"
                    data-capacidad-espiral="{{ $articulo->Capacidad_Espiral }}"
                    data-tamano-espiral="{{ $articulo->Tamano_Espiral }}">
                    {{ $articulo->Txt_Descripcion }} ({{ $articulo->Txt_Codigo }})
                </div>
            @endforeach
        </div>
    </div>
    <div class="card-footer">
        <center><button id="saveChangesBtn" class="btn btn-success">Guardar Cambios</button></center>
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
                                                                <div class="seleccion-vacia"
                                                                    style="display: {{ isset($seleccion->Id_Articulo) ? 'none' : 'block' }};">
                                                                    <p class="text-muted">Selección vacía</p>
                                                                </div>
                                                                <!-- Contenido visible cuando hay una selección -->
                                                                <div class="contenido-seleccion"
                                                                    style="display: {{ isset($seleccion->Id_Articulo) ? 'block' : 'none' }};">
                                                                    <div class="contenido-header">
                                                                        <img src="/Images/product.png" class="img-fluid"
                                                                            alt="Artículo"
                                                                            style="max-height: 100px; min-width: 100px; min-height: 100px; max-width: 100px;">
                                                                        <p class="text-muted mt-1 TxtCodigo">
                                                                            {{ $seleccion->Txt_Codigo ?? '' }}
                                                                        </p>
                                                                        <small
                                                                            class="TxtDescripcion">{{ $seleccion->Txt_Descripcion ?? '' }}</small>
                                                                        <!-- Mostrar Tamano_Espiral si existe -->
                                                                        @if(isset($seleccion->Tamano_Espiral))
                                                                            <div class="bg-secondary tamano-espiral">
                                                                                Espiral {{ $seleccion->Tamano_Espiral }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <form class="setminmax bg-dark">
                                                                        <input type="hidden" class="IdArticulo"
                                                                            value="{{ $seleccion->Id_Articulo ?? '' }}">
                                                                        <div class="form-group d-flex align-items-center">
                                                                            <small class="mr-1">Max:</small>
                                                                            <input type="number"
                                                                                class="form-control form-control-sm CantidadMax"
                                                                                value="{{ $seleccion->Cantidad_Max ?? '' }}"
                                                                                placeholder="Máx." {{ isset($seleccion->Id_Articulo) ? '' : 'disabled' }}>
                                                                        </div>
                                                                        <div class="form-group d-flex align-items-center">
                                                                            <small class="mr-1">Min:</small>
                                                                            <input type="number"
                                                                                class="form-control form-control-sm CantidadMin"
                                                                                value="{{ $seleccion->Cantidad_Min ?? '' }}"
                                                                                placeholder="Mín." {{ isset($seleccion->Id_Articulo) ? '' : 'disabled' }}>
                                                                        </div>
                                                                        <div class="form-group d-flex align-items-center">
                                                                            <small class="mr-1">Talla:</small>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm Talla"
                                                                                value="{{ $seleccion->Talla ?? '' }}"
                                                                                placeholder="Talla" {{ isset($seleccion->Id_Articulo) ? '' : 'disabled' }}>
                                                                        </div>
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm remove-article-btn">Vaciar</button>
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

    .contenido-header {
        display: flex;
        flex-direction: column;
        height: 270px;
        /* Ajusta según sea necesario */
        align-items: center;
        justify-content: center;
    }

    .setminmax {
        padding: 10px;
    }

    .droppable-cell {
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        height: 300px;
        /* Ajusta según sea necesario */
        padding: 10px;
        vertical-align: top;
        text-align: center;
    }

    .droppable-cell.drag-over {
        border-color: #28a745;
    }

    .tamano-espiral {
        font-weight: bold;
        background-color: #f8f9fa;
        padding: 4px;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
        width: 100%;
    }
</style>
@stop

@section('js')
<script>
    // Función para inicializar drag and drop
    function setupDragAndDrop() {
        document.querySelectorAll('.draggable-item').forEach(item => {
            item.addEventListener('dragstart', e => {
                e.dataTransfer.setData('text/plain', JSON.stringify({
                    id: e.target.dataset.id,
                    codigo: e.target.dataset.codigo,
                    descripcion: e.target.dataset.descripcion,
                    capacidadEspiral: e.target.dataset.capacidadEspiral,
                    tamanoEspiral: e.target.dataset.tamanoEspiral
                }));
                e.target.classList.add('dragging');
            });
            item.addEventListener('dragend', e => {
                e.target.classList.remove('dragging');
            });
        });

        document.querySelectorAll('.droppable-cell').forEach(cell => {
            cell.addEventListener('dragover', e => {
                e.preventDefault();
                cell.classList.add('drag-over');
            });
            cell.addEventListener('dragleave', () => {
                cell.classList.remove('drag-over');
            });
            cell.addEventListener('drop', e => {
                e.preventDefault();
                cell.classList.remove('drag-over');

                const data = JSON.parse(e.dataTransfer.getData('text/plain'));

                const idArticuloElement = cell.querySelector('.IdArticulo');
                const codigoElement = cell.querySelector('.TxtCodigo');
                const descripcionElement = cell.querySelector('.TxtDescripcion');
                const cantidadMaxElement = cell.querySelector('.CantidadMax');
                const cantidadMinElement = cell.querySelector('.CantidadMin');
                const tallaElement = cell.querySelector('.Talla');
                const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
                const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

                if (!data.id) {
                    // Si no se suelta un artículo válido, limpia la celda
                    if (idArticuloElement) idArticuloElement.value = '';
                    if (codigoElement) codigoElement.textContent = '';
                    if (descripcionElement) descripcionElement.textContent = '';
                    if (cantidadMaxElement) cantidadMaxElement.value = '';
                    if (cantidadMinElement) cantidadMinElement.value = '';
                    if (seleccionVaciaElement) {
                        seleccionVaciaElement.style.display = 'block';
                    }
                    if (contenidoSeleccionElement) {
                        contenidoSeleccionElement.style.display = 'none';
                    }
                    if (cantidadMaxElement) cantidadMaxElement.disabled = true;
                    if (cantidadMinElement) cantidadMinElement.disabled = true;
                    if (tallaElement) {
                        tallaElement.value = '';
                        tallaElement.disabled = true;
                    }
                } else {
                    // Asigna los datos del artículo solo por drag and drop
                    if (idArticuloElement) idArticuloElement.value = data.id;
                    if (codigoElement) codigoElement.textContent = data.codigo;
                    if (descripcionElement) descripcionElement.textContent = data.descripcion;
                    // Solo se asigna Capacidad_Espiral si no hay valor registrado previamente
                    if (cantidadMaxElement && (!cantidadMaxElement.value || cantidadMaxElement.value === "0")) {
                        cantidadMaxElement.value = data.capacidadEspiral;
                    }
                    if (cantidadMinElement) cantidadMinElement.value = 0;
                    if (seleccionVaciaElement) {
                        seleccionVaciaElement.style.display = 'none';
                    }
                    if (contenidoSeleccionElement) {
                        contenidoSeleccionElement.style.display = 'block';
                    }
                    if (cantidadMaxElement) cantidadMaxElement.disabled = false;
                    if (cantidadMinElement) cantidadMinElement.disabled = false;
                    if (tallaElement) tallaElement.disabled = false;

                    // Mostrar Tamano_Espiral en la celda (simulando un th)
                    let tamanoElement = cell.querySelector('.tamano-espiral');
                    if (!tamanoElement) {
                        tamanoElement = document.createElement('div');
                        tamanoElement.classList.add('tamano-espiral');
                        cell.insertBefore(tamanoElement, cell.firstChild);
                    }
                    tamanoElement.textContent = data.tamanoEspiral;
                }
            });
        });

        document.querySelectorAll('.remove-article-btn').forEach(button => {
            button.addEventListener('click', e => {
                const cell = e.target.closest('.droppable-cell');
                const idArticuloElement = cell.querySelector('.IdArticulo');
                const codigoElement = cell.querySelector('.TxtCodigo');
                const descripcionElement = cell.querySelector('.TxtDescripcion');
                const cantidadMaxElement = cell.querySelector('.CantidadMax');
                const cantidadMinElement = cell.querySelector('.CantidadMin');
                const tallaElement = cell.querySelector('.Talla');
                const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
                const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');
                if (idArticuloElement) idArticuloElement.value = '';
                if (codigoElement) codigoElement.textContent = '';
                if (descripcionElement) descripcionElement.textContent = '';
                if (cantidadMaxElement) cantidadMaxElement.value = '';
                if (cantidadMinElement) cantidadMinElement.value = '';
                if (tallaElement) tallaElement.value = '';
                if (seleccionVaciaElement) {
                    seleccionVaciaElement.style.display = 'block';
                }
                if (contenidoSeleccionElement) {
                    contenidoSeleccionElement.style.display = 'none';
                }
                if (cantidadMaxElement) cantidadMaxElement.disabled = true;
                if (cantidadMinElement) cantidadMinElement.disabled = true;
                if (tallaElement) tallaElement.disabled = true;
            });
        });
    }

    // Verificar el estado de las celdas al cargar la página
    document.querySelectorAll('.droppable-cell').forEach(cell => {
        const idArticuloElement = cell.querySelector('.IdArticulo');
        const cantidadMaxElement = cell.querySelector('.CantidadMax');
        const cantidadMinElement = cell.querySelector('.CantidadMin');
        const tallaElement = cell.querySelector('.Talla');
        const seleccionVaciaElement = cell.querySelector('.seleccion-vacia');
        const contenidoSeleccionElement = cell.querySelector('.contenido-seleccion');

        if (idArticuloElement && !idArticuloElement.value) {
            if (seleccionVaciaElement) {
                seleccionVaciaElement.style.display = 'block';
            }
            if (contenidoSeleccionElement) {
                contenidoSeleccionElement.style.display = 'none';
            }
            if (cantidadMaxElement) cantidadMaxElement.disabled = true;
            if (cantidadMinElement) cantidadMinElement.disabled = true;
            if (tallaElement) tallaElement.disabled = true;
        } else {
            if (seleccionVaciaElement) {
                seleccionVaciaElement.style.display = 'none';
            }
            if (contenidoSeleccionElement) {
                contenidoSeleccionElement.style.display = 'block';
            }
            if (cantidadMaxElement) cantidadMaxElement.disabled = false;
            if (cantidadMinElement) cantidadMinElement.disabled = false;
            if (tallaElement) tallaElement.disabled = false;
        }
    });

    // Búsqueda en la barra lateral
    document.getElementById('searchBar').addEventListener('input', function () {
        const query = this.value;
        const resultsContainer = document.getElementById('searchResults');

        if (query.length === 0) {
            resultsContainer.innerHTML = '';
            @foreach ($articulos as $articulo)
                resultsContainer.innerHTML += `
                            <div class="list-group-item draggable-item"
                                 draggable="true"
                                 data-id="{{ $articulo->Id_Articulo }}"
                                 data-codigo="{{ $articulo->Txt_Codigo }}"
                                 data-descripcion="{{ $articulo->Txt_Descripcion }}"
                                 data-capacidad-espiral="{{ $articulo->Capacidad_Espiral }}"
                                 data-tamano-espiral="{{ $articulo->Tamano_Espiral }}">
                                {{ $articulo->Txt_Descripcion }} ({{ $articulo->Txt_Codigo }})
                            </div>
                        `;
            @endforeach
            setupDragAndDrop();
            return;
        }

        fetch(`/admin/config/plano/1?search=${query}`)
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item draggable-item';
                    div.draggable = true;
                    div.dataset.id = item.Id_Articulo;
                    div.dataset.codigo = item.Txt_Codigo;
                    div.dataset.descripcion = item.Txt_Descripcion;
                    div.dataset.capacidadEspiral = item.Capacidad_Espiral;
                    div.dataset.tamanoEspiral = item.Tamano_Espiral;
                    div.textContent = `${item.Txt_Descripcion} (${item.Txt_Codigo})`;
                    resultsContainer.appendChild(div);
                });
                setupDragAndDrop();
            })
            .catch(error => console.error('Error:', error));
    });

    setupDragAndDrop();

    document.getElementById('saveChangesBtn').addEventListener('click', () => {
        const updatedData = [];
        let valid = true;

        document.querySelectorAll('.droppable-cell').forEach(cell => {
            const idArticulo = cell.querySelector('.IdArticulo')?.value || null;
            const cantidadMax = Number(cell.querySelector('.CantidadMax')?.value) || 0;
            const cantidadMin = Number(cell.querySelector('.CantidadMin')?.value) || 0;
            const talla = cell.querySelector('.Talla')?.value || '';

            if (idArticulo && (cantidadMax === 0 || cantidadMin === 0)) {
                valid = false;
            }

            updatedData.push({
                idConfiguracion: cell.dataset.id,
                idArticulo: idArticulo,
                cantidadMax: cantidadMax,
                cantidadMin: cantidadMin,
                talla: talla
            });
        });

        if (!valid) {
            alert("Por favor, asigne valores mayores a 0 para Máximo y Mínimo en los artículos asignados.");
            return;
        }

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

    // Hacer la tarjeta movible
    const card = document.getElementById('draggableCard');
    const cardHeader = document.getElementById('cardHeader');
    let offsetX, offsetY;
    let isDragging = false;

    cardHeader.addEventListener('mousedown', (e) => {
        offsetX = e.clientX - card.getBoundingClientRect().left;
        offsetY = e.clientY - card.getBoundingClientRect().top;
        isDragging = true;
        document.body.style.userSelect = 'none';
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            card.style.left = e.clientX - offsetX + 'px';
            card.style.top = e.clientY - offsetY + 'px';
        }
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.style.userSelect = '';
    });

    const transparencyControl = document.getElementById('transparencyControl');
    if (transparencyControl) {
        transparencyControl.addEventListener('input', () => {
            const transparencyValue = transparencyControl.value;
            card.style.opacity = transparencyValue / 100;
        });
    }
</script>

<script>
    function goBack() {
        window.history.back();
    }
</script>
@stop