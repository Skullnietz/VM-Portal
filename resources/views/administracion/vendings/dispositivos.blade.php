@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Dispositivos'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Dispositivos') }}</h4>
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
                        <h5 class="card-title">
                            Example Title
                        </h5>
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
                        Lorem ipsum dolor sit am
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
@stop

@section('css')
@stop

@section('js')
<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop
