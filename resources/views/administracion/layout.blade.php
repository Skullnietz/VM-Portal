@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Layout'))

@section('content_header')



    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Layout | Planograma') }}</h4>
            </div>
            <div class="col-md-3 col-3 ml-auto">
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Layout VM</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <a href="#" class="dropdown-item">Action</a>
                                    <a href="#" class="dropdown-item">Another action</a>
                                    <a href="#" class="dropdown-item">Something else here</a>
                                    <a class="dropdown-divider"></a>
                                    <a href="#" class="dropdown-item">Separated link</a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table>
                                <tr>
                                    <th><i class="fas fa-layer-group"></i> Capacidad</th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                    <th style="width:150px;"><input style="text-align: center" class="form-control" type="text" value="24"></th>
                                </tr>
                                <tr>
                                    <td class="charola">CHAROLA 1</td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <a class="btn btn-success"><i class="fas fa-plus-circle fa-1x"></i><img style="height:30px; filter: invert(.99);" src="Images/product.png" alt="Producto"></a>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>LENTE POLIC. MICA CLARA, ANTIEMPAÑO MOD.90960-AF U</div>
                                        </div>


                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>LENTE POLIC. MICA CLARA, ANTIEMPAÑO MOD.90960-AF U</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>GUANTE ALGODON PUNTOS PVC PESO MEDIO (A-2003GVPVC)</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>GUANTE TEJIDO DE NYLON BLANCO RIBETE NARANJA MOD.</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>TAPON NRR29 ESPUMA EXPANDIBLE CON CORDON MOD. 1110</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>TAPON NRR29 ESPUMA EXPANDIBLE CON CORDON MOD. 1110</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>GUANTE DE LATEX SIN FLOCADO 11 ROJO</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>GUANTE DE LATEX SIN FLOCADO 11 ROJO</div>
                                        </div>
                                    </td>
                                    <td class="product-cell">
                                        <div class="bmodal" data-toggle="modal" data-target="#productModal">
                                            <div class="product-image"><img src="Images/product.png" alt="Producto"></div>
                                            <div>GUANTE DE LATEX SIN FLOCADO 11 ROJO</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="bg-warning">
                                    <td>
                                        <b><i class="fas fa-bell"></i> Cantidad</b>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text" id="basic-addon3">Min</span>
                                            </div>
                                            <input type="number" min="0" class="form-control">
                                        </div>
                                    </td>
                                    

                                </tr>
                                <tfoot>
                                    <tr class="bg-dark">
                                        <td><i class="fas fa-calculator"></i> Selección</td>
                                        <td>10</td>
                                        <td>11</td>
                                        <td>12</td>
                                        <td>13</td>
                                        <td>14</td>
                                        <td>15</td>
                                        <td>16</td>
                                        <td>17</td>
                                        <td>18</td>
                                        <td>19</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Editar Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <center>
                        <div class="form-group">
                            <label for="productName">Nombre del Producto</label><br>
                            <select style="width:330px" class="" id="productName" required>
                                <option value="Producto1">Producto 1</option>
                                <option value="Producto2">Producto 2</option>
                                <option value="Producto3">Producto 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Descripción</label>
                            <input style="width:330px" type="text" class="form-control" id="productDescription" required>
                        </div>
                        <div class="form-group">
                            <label for="productImage">Imagen del Producto (URL)</label>
                            <input style="width:330px" type="text" class="form-control" id="productImage" required>
                        </div>
                        <input type="hidden" id="currentBmodal">
                    </form>
                </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
@stop

@section('css')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
        margin: 20px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        min-width: 150px;
        text-align: center;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    .product-cell {
        background-color: #f2f2f2;
    }
    .product-header {
        color: red;
    }
    .product-image img {
        width: 50px;
    }
    .charola {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
    }
    .bmodal {
        cursor: pointer;
        padding: 5px;
        background-color: #4c7faf11;
        border-width: 1px;
        border-style: solid;
        border-color: rgba(0, 38, 255, 0.541);
        border-radius: 10px;
    }
</style>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
<script>

    $(document).ready(function() {
        // Inicializa Select2
        $('#productName').select2();

        $('#productModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var bmodal = button.closest('.bmodal'); // Find the closest bmodal div
            var modal = $(this);
            var productName = bmodal.find('.product-image img').attr('alt');
            var productDescription = bmodal.find('div:nth-child(2)').text();
            var productImage = bmodal.find('.product-image img').attr('src');

            modal.find('#productName').val(productName || '').trigger('change');
            modal.find('#productDescription').val(productDescription || '');
            modal.find('#productImage').val(productImage || '');
            modal.find('#currentBmodal').val(bmodal[0].outerHTML);
        });

        window.saveProduct = function() {
            var modal = $('#productModal');
            var productName = modal.find('#productName').val();
            var productDescription = modal.find('#productDescription').val();
            var productImage = modal.find('#productImage').val();
            var currentBmodal = modal.find('#currentBmodal').val();

            if (productName && productDescription && productImage) {
                var newBmodal = `
                    <div class="bmodal" data-toggle="modal" data-target="#productModal">
                        <div class="product-image"><img src="${productImage}" alt="${productName}"></div>
                        <div>${productName}</div>
                    </div>
                `;

                $('.bmodal').each(function() {
                    if (this.outerHTML === currentBmodal) {
                        $(this).replaceWith(newBmodal);
                    }
                });

                modal.modal('hide');
            }
        };
    });

    function goBack() {
        window.history.back();
    }
</script>
@stop
