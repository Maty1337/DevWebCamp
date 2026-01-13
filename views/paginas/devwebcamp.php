<main class="devwebcamp">
    <h2 class="devwebcamp__heading"><?php echo $titulo; ?></h2>
    <p class="devwebcamp__descripcion">Información sobre DevWebCamp, el evento de desarrollo web más importante.</p>


    <div class="devwebcamp__grid">
        <div <?php aos_animacion() ?> class="devwebcamp__imagen">
            <picture>
                <source srcset="build/img/sobre_devwebcamp.avif" type="image/avif">
                <source srcset="build/img/sobre_devwebcamp.webp" type="image/webp">
                <img loading="lazy" width="200" height="300" src="build/img/sobre_devwebcamp.jpg" alt="Imagen sobre DevWebCamp">
            </picture>
        </div>

        <div <?php aos_animacion() ?> class="devwebcamp__contenido">
            <p <?php aos_animacion() ?> class="devwebcamp__texto">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Commodi, doloribus voluptatem, soluta inventore facilis, incidunt ex animi unde dicta sunt dolorum rem optio maiores velit placeat cupiditate asperiores? Nisi, a!</p>
            <p <?php aos_animacion() ?> class="devwebcamp__texto">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Commodi, doloribus voluptatem, soluta inventore facilis, incidunt ex animi unde dicta sunt dolorum rem optio maiores velit placeat cupiditate asperiores? Nisi, a!</p>
        </div>
    </div>
</main>