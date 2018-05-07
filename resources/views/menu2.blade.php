
<div class="swanky">
  <!--/////////// Begin Dropdown ////////////
  -->
  <div class="swanky_wrapper">
    
    <input id="Documentos" name="radio" type="radio"></input>
    <label for="Documentos">
      <i class="fa fa-2x fa-files-o"></i>
      <span>Documentos</span>
      <div class="lil_arrow"></div>
      <div class="bar"></div>
      <div class="swanky_wrapper__content">
        <ul>
          @if(auth()->user()->isUser())
          <a href="{{ url('documentos') }}"><li>Nuevo documento</li></a>
          @endif 
          <a href="{{ url('historial') }}"><li>Seguimiento</li></a>
          <a href="{{ url('resumen') }}"><li>Resumen</li></a>
        </ul>
      </div>
    </label>
    @if(auth()->user()->isPublisher())
    <input id="Reportes" name="radio" type="radio"></input>
    <label for="Reportes">
      <i class="fa fa-2x fa-info-circle"></i>
      <span>Reportes</span>
      <div class="lil_arrow"></div>
      <div class="bar"></div>
      <div class="swanky_wrapper__content">
        <ul>
          <a href="{{ url('/avance') }}"><li>Avance</li></a>
        </ul>
      </div>
    </label>
    @endif
    
    @if(auth()->user()->isAdmin())
    <input id="Catálogos" name="radio" type="radio"></input>
    <label for="Catálogos">
      <i class="fa fa-database fa-2x"></i>
      <span>Catálogos</span>
      <div class="lil_arrow"></div>
      <div class="bar"></div>
      <div class="swanky_wrapper__content">
        <ul>
            <a href="{{ url('usuarios') }}"><li>Usuarios</li></a>
            <a href="{{ url('entidades') }}"><li>Entidades</li></a>
        </ul>
      </div>
    </label>
    @endif 
    
    <input id="Sistema" name="radio" type="radio"></input>
    <label for="Sistema">
      <i class="fa fa-2x fa-terminal"></i>
      <span>Sistema</span>
      <div class="lil_arrow"></div>
      <div class="bar"></div>
      <div class="swanky_wrapper__content">
        <ul>
          <a href="{{ url('opciones') }}"><li>Opciones</li></a>
          <a href="{{ url('logout') }}"><li>Salir</li></a>
        </ul>
      </div>
    </label>
    
  </div>
  <!--/////////// End Dropdown ////////////
  -->
</div>
