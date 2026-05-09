<div class="user-panel mt-3 pb-3 mb-3 d-flex">
    @if(config('adminlte.usermenu_image'))
        <div class="image">
            @if(isset($_SESSION['usuario']->Id_Planta))
                <img src="/Images/Plantas/{{$_SESSION['usuario']->Id_Planta}}.png" class="img-circle elevation-2" alt="User Image" style="background: white;">
            @else
                <img src="/Images/Plantas/urvina-2.png" class="img-circle elevation-2" alt="User Image" style="background: white;">
            @endif
        </div>
    @endif
    <div class="info">
        <a href="#" class="d-block">
             <?php
                if (isset($_SESSION['usuario']->Txt_Nombre) && isset($_SESSION['usuario']->Txt_ApellidoP)) {
                    echo $_SESSION['usuario']->Txt_Nombre . " " . $_SESSION['usuario']->Txt_ApellidoP;
                } else {
                    echo $_SESSION['usuario']->Nick_Usuario ?? 'Usuario';
                }
            ?>
        </a>
    </div>
</div>
