
<div class="swanky">
  <!--/////////// Begin Dropdown ////////////
  -->
  <div class="swanky_wrapper">
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
    <input id="Documentos" name="radio" type="radio"></input>
    <label for="Documentos">
      <i class="fa fa-2x fa-files-o"></i>
      <span>Documentos</span>
      <div class="lil_arrow"></div>
      <div class="bar"></div>
      <div class="swanky_wrapper__content">
        <ul>
          <a href="{{ url('documentos') }}"><li>Nuevo documento</li></a>
          <a href="{{ url('historial') }}"><li>Revisión</li></a>
          <a href="{{ url('publicacion') }}"><li>Publicación</li></a>
        </ul>
      </div>
    </label>
    <input id="Reportes" name="radio" type="radio"></input>
    <label for="Reportes">
      <i class="fa fa-2x fa-info-circle"></i>
      <span>Reportes</span>
      <div class="lil_arrow"></div>
      <div class="bar"></div>
      <div class="swanky_wrapper__content">
        <ul>
          <a href="{{ url('reportes/avance') }}"><li>Avance</li></a>
        </ul>
      </div>
    </label>
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
