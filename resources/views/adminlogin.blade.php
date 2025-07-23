

<style>
    @import  url(//fonts.googleapis.com/css?family=Lato:300:400);

    body {
        margin: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;

        /* Fondo personalizado */
        background-image: url('/Images/Backgrounds/bgadmin.png'); /* <- cambia esto por el path real */
        background-size: cover;
        background-repeat: no-repeat; 
    }

    h1 {
        font-family: 'Lato', sans-serif;
        font-weight: 300;
        letter-spacing: 2px;
        font-size: 48px;
    }

    p {
        font-family: 'Lato', sans-serif;
        letter-spacing: 1px;
        font-size: 14px;
        color: #333333;
    }

    .header {
        text-align: center;
        background: linear-gradient(60deg, rgba(84, 58, 183, 1) 0%, rgba(0, 172, 193, 1) 100%);
        color: white;
    }

    .content {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .waves {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 15vh;
        margin-bottom: -7px; /*Fix for safari gap*/
        min-height: 300px;
        max-height: 150px;
    }

    /* Animation */
    .parallax > use {
        animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
    }

    .parallax > use:nth-child(1) {
        animation-delay: -2s;
        animation-duration: 7s;
    }

    .parallax > use:nth-child(2) {
        animation-delay: -3s;
        animation-duration: 10s;
    }

    .parallax > use:nth-child(3) {
        animation-delay: -4s;
        animation-duration: 13s;
    }

    .parallax > use:nth-child(4) {
        animation-delay: -5s;
        animation-duration: 20s;
    }

    @keyframes  move-forever {
        0% {
            transform: translate3d(-90px, 0, 0);
        }

        100% {
            transform: translate3d(85px, 0, 0);
        }
    }

    /*Shrinking for mobile*/
    @media (max-width: 768px) {
        .waves {
            height: 40px;
            min-height: 40px;
        }

        .content {
            height: 30vh;
        }

        h1 {
            font-size: 24px;
        }
    }
</style>


<!-- Waves Container -->
<svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
    <defs>
        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
    </defs>
    <g class="parallax">
    <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(14, 126, 45, 0.7)" />
    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(14, 126, 45, 0.5)" />
    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(15, 25, 52, 0.3)" />
    <use xlink:href="#gentle-wave" x="48" y="7" fill="#0F1934" />
    </g>
</svg>

<!DOCTYPE html>
<html lang="">

<head>

    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="gtxQlUPHKJUaYKPOKO1RTuNmTOuMnbRkU9L9ixHD">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    

    
    
    
    <title>
                USI :: Admin :: VM            </title>

    
        <link rel="stylesheet" href="http://127.0.0.1:8000/vendor/icheck-bootstrap/icheck-bootstrap.min.css">

    
            <link rel="stylesheet" href="/vendor/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="/vendor/overlayScrollbars/css/OverlayScrollbars.min.css">
        <link rel="stylesheet" href="/vendor/adminlte/dist/css/adminlte.min.css">

                    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
            
    
    
    
    
    
            
    
    
</head>

<body class="login-page" >

    
        <div class="login-box">

        
        <div class="login-logo">
            <a href="http://127.0.0.1:8000/home">

                
                                    <img src="http://127.0.0.1:8000/vendor/adminlte/dist/img/vending-machine2.png"
                         alt="Admin Logo" height="200">
                
                

            </a>
        </div>

        
        <div class="card card-outline card-primary" style="border-radius:15px">

            
                <div class="card-header ">
                    <h3 class="card-title float-none text-center">
                        <b>Portal de Administracion VM</b>
                    </h3>
                </div>
            
            
            <div class="card-body login-card-body " style="border-radius:15px">
                
    
    
    <form method="POST" action="admin/validar-admin">
        @csrf
        
        <div class="input-group mb-3">
            <input type="text" name="usuario" class="form-control "
                   value="" placeholder="Usuario" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope "></span>
                </div>
            </div>

                    </div>

        
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control "
                   placeholder="Contraseña">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock "></span>
                </div>
            </div>

                    </div>

        
        <div class="row">
            <div class="col-7">
                
            </div>

            <div class="col-5">
                <button type="submit" class="btn btn-block btn-flat btn-primary">
                    <span class="fas fa-sign-in-alt"></span>
                    Entrar
                </button>
            </div>
        </div>

    </form>
            </div>

            
                            
            
        </div>

    </div>

    
            <script src="http://127.0.0.1:8000/vendor/jquery/jquery.min.js"></script>
        <script src="http://127.0.0.1:8000/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="http://127.0.0.1:8000/vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="http://127.0.0.1:8000/vendor/adminlte/dist/js/adminlte.min.js"></script>
    
    
    
    
    
    
            
</body>

</html>
