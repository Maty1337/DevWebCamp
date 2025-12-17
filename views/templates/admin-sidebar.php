<aside class="dashboard__sidebar">
    <nav class="dashboard__menu">

        <a href="/admin/dashboard" class="dashboard__enlace <?php echo paginaActual('/dashboard') ? 'dashboard__enlace--actual' : '' ?>">
            <i class="fa-solid fa-house dashboard__icono"></i>
            <span class="dashboard__menu-texto">Inicio</span>
        </a>

        <a href="/admin/ponentes" class="dashboard__enlace <?php echo paginaActual('/ponentes') ? 'dashboard__enlace--actual' : '' ?>">
            <i class="fa-solid fa-microphone dashboard__icono"></i>
            <span class="dashboard__menu-texto">Ponentes</span>
        </a>

        <a href="/admin/eventos" class="dashboard__enlace <?php echo paginaActual('/eventos') ? 'dashboard__enlace--actual' : '' ?>">
            <i class="fa-solid fa-calendar-days dashboard__icono"></i>
            <span class="dashboard__menu-texto">Eventos</span>
        </a>

        <a href="/admin/registrados" class="dashboard__enlace <?php echo paginaActual('/registrados') ? 'dashboard__enlace--actual' : '' ?>">
            <i class="fa-solid fa-users dashboard__icono"></i>
            <span class="dashboard__menu-texto">Registrados</span>
        </a>

        <a href="/admin/regalos" class="dashboard__enlace <?php echo paginaActual('/regalos') ? 'dashboard__enlace--actual' : '' ?>">
            <i class="fa-solid fa-box dashboard__icono"></i>
            <span class="dashboard__menu-texto">Regalos</span>
        </a>

        <a href="/logout" class="dashboard__enlace">
            <i class="fa-solid fa-right-from-bracket dashboard__icono"></i>
            <span class="dashboard__menu-texto">Cerrar sesión</span>
        </a>

    </nav>
</aside>
