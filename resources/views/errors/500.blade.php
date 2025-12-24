@extends('errors.layout')

@section('title', 'Error del Servidor')
@section('code', '500')
@section('message', 'Algo salió mal en nuestros servidores.')

@section('content')
    <a href="{{ url('/') }}" class="btn-home">Regresar al Inicio</a>
@endsection