@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@php( $profile_url = View::getSection('profile_url') ?? config('adminlte.profile_url', 'logout') )
@php
    $unreadNotifications = \App\Http\Controllers\NotificationController::showNotifications();
@endphp

<li class="nav-item dropdown user-menu">

    {{-- User menu toggler --}}
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
        @if(config('adminlte.usermenu_image'))
            @if(isset($_SESSION['usuario']->Id_Planta))
                <img src="/Images/Plantas/{{$_SESSION['usuario']->Id_Planta}}.png"
                    class="user-image img-circle elevation-2 shadow-sm" alt="User Image"
                    style="background: white; padding: 2px;">
            @else
                <img src="/Images/Plantas/urvina-2.png" class="user-image img-circle elevation-2 shadow-sm" alt="User Image"
                    style="background: white; padding: 2px;">
            @endif
        @endif
        <span @if(config('adminlte.usermenu_image')) class="d-none d-md-inline font-weight-bold" @endif>
            {{-- Name hidden on mobile, visible on desktop --}}
        </span>
    </a>

    {{-- User menu dropdown --}}
    <!-- Removed inline animation style, handled by CSS class below -->
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow-lg border-0"
        style="border-radius: 15px; overflow: hidden;">

        {{-- User menu header --}}
        @if(!View::hasSection('usermenu_header') && config('adminlte.usermenu_header'))
                <li class="user-header text-white"
                    style="background: linear-gradient(90deg, #0ea5a5, #0ea56a); height: auto; min-height: 175px; padding-top: 30px;">
                    @if(config('adminlte.usermenu_image'))
                        @if(isset($_SESSION['usuario']->Id_Planta))
                            <img src="/Images/Plantas/{{$_SESSION['usuario']->Id_Planta}}.png" class="img-circle elevation-2 shadow-lg"
                                alt="User Image"
                                style="background: white; padding: 4px; width: 90px; height: 90px; object-fit: contain;">
                        @else
                            <img src="/Images/Plantas/urvina-2.png" class="img-circle elevation-2 shadow-lg" alt="User Image"
                                style="background: white; padding: 4px; width: 90px; height: 90px; object-fit: contain;">
                        @endif
                    @endif
                    <p class="mt-2 font-weight-bold" style="font-size: 1.1em;">
                        <?php
            $fullname = $_SESSION['usuario']->Txt_Nombre . " " . $_SESSION['usuario']->Txt_ApellidoP;
                                                ?>
                        {{ Str::limit($fullname, 20) }}
                        <small class="d-block font-weight-light">{{ $_SESSION['usuario']->Txt_Puesto ?? 'Cliente' }}</small>
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
        <li class="user-footer bg-white d-flex justify-content-between p-3" style="border-radius: 0 0 15px 15px;">

            <a class="btn btn-outline-success shadow-sm px-4 font-weight-bold"
                href="{{ route('client.profile', ['language' => request()->route('language') ?? 'es']) }}">
                <i class="fa fa-fw fa-user mr-1"></i>
                Perfil
            </a>

            <a class="btn btn-outline-danger shadow-sm px-4 font-weight-bold" href="/logout">
                <i class="fa fa-fw fa-power-off mr-1"></i>
                Salir
            </a>

        </li>

    </ul>

    <style>
        /* CSS Trick for Enter/Exit Animations */
        .user-menu .dropdown-menu {
            display: block !important;
            /* Force display to allow visibility transition */
            visibility: hidden;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease-in-out;
            top: 100%;
            /* Ensure it doesn't float weirdly */
        }

        .user-menu .dropdown-menu.show {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        /* Triangle Pointer */
        .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 20px;
            width: 16px;
            height: 16px;
            background: #0ea56a;
            /* Matches the gradient start */
            transform: rotate(45deg);
            z-index: -1;
        }
    </style>

</li>