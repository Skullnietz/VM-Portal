@extends('adminlte::page')

@section('title', 'Perfil de Usuario')

@section('content_header')
    <h1><i class="fas fa-user-circle mr-2"></i> Perfil de Usuario</h1>
@stop

@section('content')
<div class="row">
    <!-- Tarjeta de Información (Left Column) -->
    <div class="col-md-4">
        <!-- Removed h-100 to prevent empty space stretching -->
        <div class="card card-urvina-sync card-widget widget-user shadow-lg">
            <!-- Header with Gradient -->
            <div class="widget-user-header text-white" style="background: linear-gradient(90deg, #0ea5a5, #0ea56a); height: auto; min-height: 135px; padding-bottom: 60px;">
                <h3 class="widget-user-username font-weight-bold">{{ $user->Txt_Nombre }} {{ $user->Txt_ApellidoP }}</h3>
                <h5 class="widget-user-desc text-light" style="font-weight: 300;">{{ $user->Txt_Puesto ?? 'Cliente' }}</h5>
            </div>
            
            <div class="widget-user-image" style="top: 85px;"> 
                @if(isset($user->Id_Planta))
                    <img class="img-circle elevation-2 shadow" src="/Images/Plantas/{{$user->Id_Planta}}.png" alt="User Avatar" style="background: white; width: 90px; height: 90px; object-fit: contain; padding: 5px;">
                @else
                    <img class="img-circle elevation-2 shadow" src="/Images/Plantas/urvina-2.png" alt="User Avatar" style="background: white; width: 90px; height: 90px; object-fit: contain; padding: 5px;">
                @endif
            </div>

            <div class="card-footer pt-5 d-flex flex-column justify-content-between" style="background-color: #0e1b2d; color: #e8f1f7; flex: 1;">
                <div class="row mt-4"> 
                    <div class="col-sm-6 border-right border-secondary">
                        <div class="description-block">
                            <h5 class="description-header text-warning">{{ $user->Nick_Usuario }}</h5>
                            <span class="description-text">USUARIO</span>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-6">
                        <div class="description-block">
                            <h5 class="description-header text-warning text-truncate" data-toggle="tooltip" title="{{ $planta }}">{{ Str::limit($planta, 15) }}</h5>
                            <span class="description-text">PLANTA</span>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
                <div class="row mt-4 mb-3">
                    <div class="col-12 text-center">
                         <span class="badge badge-success px-4 py-2 rounded-pill shadow-sm" style="font-size: 1em;">Estatus: Activo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Personal Info & Security -->
    <div class="col-md-8">
        
        <!-- Alerts Section -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check-circle"></i> Éxito!</h5>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-exclamation-triangle"></i> Atención!</h5>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <!-- Tarjeta de Información Personal -->
        <div class="card shadow-lg mb-4">
            <!-- Unified Gradient Header -->
            <div class="card-header text-white" style="background: linear-gradient(90deg, #0ea5a5, #0ea56a);">
                <h3 class="card-title"><i class="fas fa-id-card mr-2 text-white"></i> Información Personal</h3>
            </div>
            <form action="{{ route('client.profile.update') }}" method="POST">
                @csrf
                <div class="card-body">
                     <div class="form-group row">
                        <label for="nombre" class="col-sm-3 col-form-label text-right">Nombres</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-user text-secondary"></i></span>
                                </div>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $user->Txt_Nombre }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="apellidos" class="col-sm-3 col-form-label text-right">Apellidos</label>
                        <div class="col-sm-9">
                             <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-user-tag text-secondary"></i></span>
                                </div>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ $user->Txt_ApellidoP }}" required>
                            </div>
                        </div>
                    </div>
                     <div class="form-group row">
                        <label for="puesto" class="col-sm-3 col-form-label text-right">Puesto</label>
                        <div class="col-sm-9">
                             <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-briefcase text-secondary"></i></span>
                                </div>
                                <input type="text" class="form-control" id="puesto" name="puesto" value="{{ $user->Txt_Puesto }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-end pb-4 pr-5">
                    <!-- Changed to btn-success for consistency -->
                    <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm"><i class="fas fa-save mr-2"></i> Actualizar Información</button>
                </div>
            </form>
        </div>

        <!-- Tarjeta de Cambio de Contraseña -->
        <div class="card shadow-lg">
            <!-- Unified Gradient Header -->
            <div class="card-header text-white" style="background: linear-gradient(90deg, #0ea5a5, #0ea56a);">
                <h3 class="card-title"><i class="fas fa-lock mr-2 text-white"></i> Seguridad de la Cuenta</h3>
            </div>
            <form action="{{ route('client.profile.password') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="alert alert-light border-info text-info alert-dismissible fade show shadow-sm" role="alert" style="background-color: #f4fbfc;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-info-circle"></i> Nota Importante</h5>
                        <p class="mb-0">Mantenga su contraseña segura. Si la cambia, deberá usar la nueva contraseña para iniciar sesión la próxima vez.</p>
                        <small class="d-block mt-2 text-muted">Mínimo 8 caracteres, al menos una letra y un número.</small>
                    </div>

                    <div class="form-group row mt-4">
                        <label for="password" class="col-sm-3 col-form-label text-right">Nueva Contraseña</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-key text-secondary"></i></span>
                                </div>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su nueva contraseña" required minlength="8">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-3 col-form-label text-right">Confirmar Contraseña</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-check-double text-secondary"></i></span>
                                </div>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repita la nueva contraseña" required minlength="8">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex justify-content-end pb-4 pr-5">
                    <button type="submit" class="btn btn-success px-4 font-weight-bold shadow-sm"><i class="fas fa-save mr-2"></i> Actualizar Contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .widget-user .widget-user-username {
            font-size: 22px;
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .widget-user .widget-user-desc {
            font-size: 14px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        /* Fix for image centering and positioning */
        .widget-user .widget-user-image {
            left: 50%;
            margin-left: -45px;
            position: absolute;
            z-index: 10;
        }
    </style>
@stop