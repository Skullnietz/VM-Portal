@extends('adminlte::auth.login')

<style>
    @import url(//fonts.googleapis.com/css?family=Lato:300:400);

    body {
        margin: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;

        /* Fondo personalizado */
        background-image: url('/Images/Backgrounds/bguser.png'); /* <- cambia esto por el path real */
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

    @keyframes move-forever {
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
