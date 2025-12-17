<header class="dashboard__header">
    <div class="dashboard__header-grid">
        <a href="/">
            <h2 class="dashboard__logo">
                &#60; DevWebCamp />
            </h2>
        </a>

        <nav class="dashboard__nav">
            <form method="POST" class="dashboard__form" action="/logout">
                <input 
                    type="submit" 
                    class="dashboard__submit--logout" 
                    value="Cerrar Sesion"
                    formaction="/logout"
                >
            </form>
        </nav>
    </div>
</header>