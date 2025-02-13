@extends('adminlte::page')

@section('usermenu_body')
@stop

@section('title', __('Notificaciones'))

@section('content_header')
    <div class="container">
        <div class="row">
            <div class=" col-md-9 col-9">
                <h4><a href="#" onclick="goBack()" class="border rounded">&nbsp;<i
                            class="fas fa-arrow-left"></i>&nbsp;</a>&nbsp;&nbsp;&nbsp;{{ __('Notificaciones') }}</h4>
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
                        Todas las Notificaciones
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
                    <div class="container">
        @foreach($unreadNotifications as $notification)
            <div class="alert alert-info">
                <p>{{ $notification->Txt_Nombre }} error.</p>
                <p><small>Dia: {{ \Carbon\Carbon::parse($notification->Fecha)->format('d-m-Y') }} | Hora: {{ \Carbon\Carbon::parse($notification->Fecha)->format('H:i') }}</small></p>
                <p><small>Mensaje: {{$notification->description}}</small></p>
                <button class="btn btn-primary mark-as-read" data-id="{{ $notification->id }}">Marcar como leída</button>
            </div>
        @endforeach
    </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $(".mark-as-read").click(function () {
            let notificationId = $(this).data("id");
            let button = $(this);

            $.ajax({
                url: "{{ route('markNotificationAsRead', '') }}/" + notificationId,
                type: "GET",
                success: function () {
                    button.closest(".alert").fadeOut("slow", function() {
                        $(this).remove();
                    });
                },
                error: function () {
                    alert("Hubo un error al marcar la notificación como leída.");
                }
            });
        });
    });
</script>
<script>
    function goBack() {
      window.history.back();
    }
</script>
@stop


