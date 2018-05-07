@extends('base')

@section('title', 'INICIO')

@section('content')

<div class="jumbotron">
  <h1 class="display-4">Bienvenido</h1>
  <p class="lead">
      <h4>Usuario: {{ auth()->user()->Nombre }} </h4> 
  </p>
  <p class="lead">
      <h4>{{ auth()->user()->entidad->Nombre }} </h4> 
  </p>
  <p class="lead">
    <label>Rol: </label> {{ auth()->user()->Rol->NombreRol }} 
  </p>
</div>
    
@endsection

@section('script')

@endsection

