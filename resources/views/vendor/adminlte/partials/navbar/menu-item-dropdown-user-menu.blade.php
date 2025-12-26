@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') )@php
    $unreadNotifications = \App\Http\Controllers\NotificationController::showNotifications();
@endphp









<li class="nav-item dropdown user-menu">

    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        @if(config('adminlte.usermenu_image'))
            @if(isset($_SESSION['usuario']->Id_Planta))
                <img src="/Images/Plantas/{{$_SESSION['usuario']->Id_Planta}}.png" class="user-image img-circle elevation-2"
                    alt="User Image">
            @else
                <img src="/Images/Plantas/urvina-2.png" class="user-image img-circle elevation-2" alt="User Image">
            @endif
        @endif
        <span @if(config('adminlte.usermenu_image')) class="d-none d-md-inline" @endif>

        </span>
    </a>

    {{-- User menu dropdown --}}
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

        {{-- User menu header --}}
        @if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header'))
                <li class="user-header {{ config('adminlte.usermenu_header_class', 'bg-primary') }}
                                @if(!config('adminlte.usermenu_image')) h-auto @endif">
                    @if(config('adminlte.usermenu_image'))
                        @if(isset($_SESSION['usuario']->Id_Planta))
                            <img src="/Images/Plantas/{{$_SESSION['usuario']->Id_Planta}}.png" class="user-image img-circle elevation-2"
                                alt="User Image">
                        @else
                            <img src="/Images/Plantas/urvina-2.png" class="user-image img-circle elevation-2" alt="User Image">
                        @endif
                    @endif
                    <p class="@if(!config('adminlte.usermenu_image')) mt-0 @endif">
                        <?php
            $fullname = $_SESSION['usuario']->Txt_Nombre . " " . $_SESSION['usuario']->Txt_ApellidoP;
                                    ?>
                        @if (strlen($fullname) > 15)
                            {{ substr($fullname, 0, 15)}}...
                        @else
                            {{ substr($fullname, 0, 15)}}

                        @endif
                    </p>
                </li>
        @else
            @yield('usermenu_header')
        @endif

        {{-- Configured user menu links --}}
        @each('adminlte::partials.navbar.dropdown-item', $adminlte->menu("navbar-user"), 'item')

        {{-- User menu body --}}
        @hasSection('usermenu_body')
            <li class="user-body">
                @yield('usermenu_body')
            </li>
        @endif

        {{-- User menu footer --}}
        <li class="user-footer">

            <a class="btn btn-default btn-flat float-left"
                href="{{ route('client.profile', ['language' => app()->getLocale()]) }}">
                <i class="fa fa-fw fa-user text-blue"></i>
                Perfil
            </a>

            <a class="btn btn-default btn-flat float-right " href="/logout">
                <i class="fa fa-fw fa-power-off text-red"></i>
                Salir
            </a>

        </li>

    </ul>

</li>