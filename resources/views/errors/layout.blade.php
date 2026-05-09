<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <style>
        @import url('//fonts.googleapis.com/css?family=Lato:300,400');

        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-image: url('/Images/Backgrounds/bguser.png');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: 'Lato', sans-serif;
            color: #333;
            overflow: hidden;
        }

        .error-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            z-index: 10;
        }

        .error-code {
            font-size: 120px;
            font-weight: 300;
            color: #0F1934;
            margin: 0;
            line-height: 1;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .error-message {
            font-size: 32px;
            color: #0F1934;
            margin: 10px 0 30px;
            font-weight: 300;
        }

        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background-color: rgba(255, 255, 255, 0.9);
            color: #0F1934;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 400;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-home:hover {
            background-color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .waves {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 15vh;
            margin-bottom: -7px;
            min-height: 100px;
            max-height: 150px;
        }

        .parallax>use {
            animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
        }

        .parallax>use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 7s;
        }

        .parallax>use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 10s;
        }

        .parallax>use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 13s;
        }

        .parallax>use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 20s;
        }

        @keyframes move-forever {
            0% {
                transform: translate3d(-90px, 0, 0);
            }

            100% {
                transform: translate3d(85px, 0, 0);
            }
        }

        @media (max-width: 768px) {
            .error-code {
                font-size: 80px;
            }

            .error-message {
                font-size: 24px;
            }

            .waves {
                height: 40px;
                min-height: 40px;
            }
        }
    </style>
</head>

<body>

    <div class="error-container">
        <h1 class="error-code">@yield('code')</h1>
        <div class="error-message">@yield('message')</div>
        @yield('content')
    </div>

    <!-- Waves Container -->
    <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
        viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
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

</body>

</html>