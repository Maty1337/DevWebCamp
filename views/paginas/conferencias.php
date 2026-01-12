<main class="agenda">
    <h2 class="agenda__heading"><?php echo $titulo ?></h2>
    <p class="agenda__descripcion">Conoce todas las conferencias y workshops que tenemos para ti</p>
    
    <div class="eventos">
        <h3 class="eventos__heading">&lt;Conferencias /></h3>
        <p class="eventos__fecha">Viernes 10 de Noviembre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['conferencias_viernes'] as $evento) { ?>
                <?php include __DIR__ . '/../templates/evento.php'; ?>
            <?php } ?>  
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <p class="eventos__fecha">Sábado 11 de Noviembre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['conferencias_sabado'] as $evento) { ?>
                <?php include __DIR__ . '/../templates/evento.php'; ?>
            <?php } ?>   
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

    </div>

    <div class="eventos eventos--workshops">
        <h3 class="eventos__heading">&lt;Workshops /></h3>
        <p class="eventos__fecha">Viernes 10 de Noviembre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['workshops_viernes'] as $evento) { ?>
                <?php include __DIR__ . '/../templates/evento.php'; ?>
            <?php } ?>  
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <p class="eventos__fecha">Sábado 11 de Noviembre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
            <?php foreach($eventos['workshops_sabado'] as $evento) { ?>
                <?php include __DIR__ . '/../templates/evento.php'; ?>
            <?php } ?>  
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</main>