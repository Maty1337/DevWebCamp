<header class="header">
    <div class="header__contenedor">
        <nav class="header__navegacion">
            <?php if (isAuth()) { ?>
                <form method="POST" action="/logout" class="header__form">
                    <input type="submit" class="header__submit" value="Cerrar Sesion">
                </form>
                <a href="<?php echo isAdmin() ? '/admin/dashboard' : '/finalizar-registro'; ?>" class="header__enlace">Administrar</a>
            <?php } else { ?>
            <a href="/registro" class="header__enlace">Registro</a>
            <a href="/login" class="header__enlace">Iniciar Sesion</a>
            <?php } ?>
        </nav>

        <div class="header__contenido">
            <a href="/">
                <h1 class="header__logo">
                    &#60;DevWebCamp /&#62;
                </h1>
            </a>

            <p class="header__texto">Diciembre 10-11 - 2025</p>
            <p class="header__texto header__texto--modalidad">En Linea - Presencial</p>

            <a href="/registro" class="header__boton">Comprar Pase</a>
        </div>
    </div>
</header>
<div class="barra">
    <div class="barra__contenido">
        <a href="/">
            <h2 class="barra__logo">
            &#60;DevWebCamp /&#62;
            </h2>
        </a>
        <nav class="navegacion">
            <a href="/devwebcamp" class="navegacion__enlace <?php echo paginaActual('/devwebcamp') ? 'navegacion__enlace--actual' : '' ?>">Evento</a>
            <a href="/paquetes" class="navegacion__enlace <?php echo paginaActual('/paquetes') ? 'navegacion__enlace--actual' : '' ?>">Paquetes</a>
            <a href="/workshops" class="navegacion__enlace <?php echo paginaActual('/workshops') ? 'navegacion__enlace--actual' : '' ?>">Workshops / Conferencias</a>
            <a href="/registro" class="navegacion__enlace <?php echo paginaActual('/registro') ? 'navegacion__enlace--actual' : '' ?>">Comprar Pase</a>
        </nav>
    </div>
</div>