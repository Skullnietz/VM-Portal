@extends('errors.layout')

@section('title', 'Página No Encontrada')
@section('code', '404')
@section('message', '¡Ups! No pudimos encontrar la página que buscas.')

@section('content')
    <a href="{{ url('/') }}" class="btn-home">Regresar al Inicio</a>
@endsection