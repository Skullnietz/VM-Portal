@extends('adminlte::page') 

@section('usermenu_body')
@stop

@section('title', __('Articulos'))


@section('content')
<div class="container">
    
    <div class="card">
        <div class="card-header">
        <h1>Administración de Códigos CTE</h1>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">Agregar Nuevo</button>
        </div>
        <div class="card-body">
        <table class="table table-bordered" id="codigocteTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Id Artículo</th>
                <th>Artículo Descripción</th>
                <th>Artículo Código</th>
                <th>Id Planta</th>
                <th>Planta Nombre</th>
                <th>Descripción</th>
                <th>Estatus</th>
                <th>Fecha Alta</th>
                <th>Fecha Modificación</th>
                <th>Fecha Baja</th>
                <th>Id Usuario Alta</th>
                <th>Id Usuario Modificación</th>
                <th>Id Usuario Baja</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>
        </div>
    </div>
    
</div>

<!-- Modal para Crear Registro -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel">
  <div class="modal-dialog" role="document">
    <form id="createForm" action="{{ route('codigocte.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="createModalLabel">Crear Código CTE</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <!-- Campos para el formulario de creación -->
            <div class="form-group">
                <label for="Id_Articulo">Id Artículo</label>
                <input type="number" class="form-control" name="Id_Articulo" required>
            </div>
            <div class="form-group">
                <label for="Id_Planta">Id Planta</label>
                <input type="number" class="form-control" name="Id_Planta" required>
            </div>
            <div class="form-group">
                <label for="Txt_Descripcion">Descripción</label>
                <input type="text" class="form-control" name="Txt_Descripcion" required>
            </div>
            <div class="form-group">
                <label for="Txt_Estatus">Estatus</label>
                <select class="form-control" name="Txt_Estatus" required>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label for="Fecha_Alta">Fecha Alta</label>
                <input type="datetime-local" class="form-control" name="Fecha_Alta" required>
            </div>
            <div class="form-group">
                <label for="Fecha_Modificacion">Fecha Modificación</label>
                <input type="datetime-local" class="form-control" name="Fecha_Modificacion">
            </div>
            <div class="form-group">
                <label for="Fecha_Baja">Fecha Baja</label>
                <input type="datetime-local" class="form-control" name="Fecha_Baja">
            </div>
            <div class="form-group">
                <label for="Id_Usuario_Alta">Id Usuario Alta</label>
                <input type="number" class="form-control" name="Id_Usuario_Alta" required>
            </div>
            <div class="form-group">
                <label for="Id_Usuario_Modificacion">Id Usuario Modificación</label>
                <input type="number" class="form-control" name="Id_Usuario_Modificacion">
            </div>
            <div class="form-group">
                <label for="Id_Usuario_Baja">Id Usuario Baja</label>
                <input type="number" class="form-control" name="Id_Usuario_Baja">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal para Editar Registro -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
  <div class="modal-dialog" role="document">
    <form id="editForm" action="{{ route('codigocte.update') }}" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" name="Id_CodigoCte" id="editId_CodigoCte">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="editModalLabel">Editar Código CTE</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <!-- Campos para el formulario de edición -->
            <div class="form-group">
                <label for="edit_Id_Articulo">Id Artículo</label>
                <input type="number" class="form-control" name="Id_Articulo" id="edit_Id_Articulo" required>
            </div>
            <div class="form-group">
                <label for="edit_Id_Planta">Id Planta</label>
                <input type="number" class="form-control" name="Id_Planta" id="edit_Id_Planta" required>
            </div>
            <div class="form-group">
                <label for="edit_Txt_Descripcion">Descripción</label>
                <input type="text" class="form-control" name="Txt_Descripcion" id="edit_Txt_Descripcion" required>
            </div>
            <div class="form-group">
                <label for="edit_Txt_Estatus">Estatus</label>
                <select class="form-control" name="Txt_Estatus" id="edit_Txt_Estatus" required>
                    <option value="Alta">Alta</option>
                    <option value="Baja">Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_Fecha_Alta">Fecha Alta</label>
                <input type="datetime-local" class="form-control" name="Fecha_Alta" id="edit_Fecha_Alta" required>
            </div>
            <div class="form-group">
                <label for="edit_Fecha_Modificacion">Fecha Modificación</label>
                <input type="datetime-local" class="form-control" name="Fecha_Modificacion" id="edit_Fecha_Modificacion">
            </div>
            <div class="form-group">
                <label for="edit_Fecha_Baja">Fecha Baja</label>
                <input type="datetime-local" class="form-control" name="Fecha_Baja" id="edit_Fecha_Baja">
            </div>
            <div class="form-group">
                <label for="edit_Id_Usuario_Alta">Id Usuario Alta</label>
                <input type="number" class="form-control" name="Id_Usuario_Alta" id="edit_Id_Usuario_Alta" required>
            </div>
            <div class="form-group">
                <label for="edit_Id_Usuario_Modificacion">Id Usuario Modificación</label>
                <input type="number" class="form-control" name="Id_Usuario_Modificacion" id="edit_Id_Usuario_Modificacion">
            </div>
            <div class="form-group">
                <label for="edit_Id_Usuario_Baja">Id Usuario Baja</label>
                <input type="number" class="form-control" name="Id_Usuario_Baja" id="edit_Id_Usuario_Baja">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){
    // Inicialización de DataTable
    var table = $('#codigocteTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("codigocte") }}',
            // Si la respuesta JSON no tiene la propiedad "data", ajustar dataSrc en consecuencia
            dataSrc: 'data'
        },
        columns: [
            { data: 'Id_CodigoCte' },
            { data: 'Id_Articulo' },
            { data: 'ArticuloDescripcion' },
            { data: 'ArticuloCodigo' },
            { data: 'Id_Planta' },
            { data: 'PlantaNombre' },
            { data: 'Txt_Descripcion' },
            { data: 'Txt_Estatus' },
            { data: 'Fecha_Alta' },
            { data: 'Fecha_Modificacion' },
            { data: 'Fecha_Baja' },
            { data: 'Id_Usuario_Alta' },
            { data: 'Id_Usuario_Modificacion' },
            { data: 'Id_Usuario_Baja' },
            { 
              data: null, 
              orderable: false,
              render: function(data, type, row){
                  return '<button class="btn btn-sm btn-primary edit-btn" data-id="'+data.Id_CodigoCte+'">Editar</button>';
              }
            }
        ]
    });

    // Evento para abrir el modal de edición con la información del registro
    $('#codigocteTable').on('click', '.edit-btn', function(){
        var data = table.row( $(this).parents('tr') ).data();
        $('#editId_CodigoCte').val(data.Id_CodigoCte);
        $('#edit_Id_Articulo').val(data.Id_Articulo);
        $('#edit_Id_Planta').val(data.Id_Planta);
        $('#edit_Txt_Descripcion').val(data.Txt_Descripcion);
        $('#edit_Txt_Estatus').val(data.Txt_Estatus);
        // Es posible que necesites formatear las fechas al formato 'YYYY-MM-DDTHH:MM'
        $('#edit_Fecha_Alta').val(data.Fecha_Alta);
        $('#edit_Fecha_Modificacion').val(data.Fecha_Modificacion);
        $('#edit_Fecha_Baja').val(data.Fecha_Baja);
        $('#edit_Id_Usuario_Alta').val(data.Id_Usuario_Alta);
        $('#edit_Id_Usuario_Modificacion').val(data.Id_Usuario_Modificacion);
        $('#edit_Id_Usuario_Baja').val(data.Id_Usuario_Baja);
        
        $('#editModal').modal('show');
    });
});
</script>
@endsection
