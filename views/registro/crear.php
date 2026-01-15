<main class="registro">
  <h2 class="registro__heading"><?php echo $titulo ?></h2>
  <p class="registro__descripcion">Elige tu plan</p>

  <div class="paquetes__grid">
    <div class="paquete">
      <h3 class="paquete__nombre">Pase Gratis</h3>
      <ul class="paquete__lista">
        <li class="paquete__elemento">Acceso Virtual a DevWebCamp</li>
      </ul>

      <p class="paquete__precio">$0</p>

      <form method="POST" action="/finalizar-registro/gratis">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <input type="submit" class="paquetes__submit" value="Obtener Pase Gratis">
      </form>
    </div>

    <div class="paquete">
      <h3 class="paquete__nombre">Pase Presencial</h3>
      <ul class="paquete__lista">
        <li class="paquete__elemento">Acceso Presencial a DevWebCamp</li>
        <li class="paquete__elemento">Pase a los dos dias</li>
        <li class="paquete__elemento">Acceso a Talleres y Conferencias</li>
        <li class="paquete__elemento">Acceso a las Grabaciones</li>
        <li class="paquete__elemento">Camisa del evento</li>
        <li class="paquete__elemento">Comida y Bebida</li>
      </ul>

      <p class="paquete__precio">$199</p>

      
      <div
        class="paypal-button"
        data-paquete-id="1"
        data-token="<?php echo $token; ?>"
        id="paypal-presencial"></div>
    </div>

    <div class="paquete">
      <h3 class="paquete__nombre">Pase Virtual</h3>
      <ul class="paquete__lista">
        <li class="paquete__elemento">Acceso Virtual a DevWebCamp</li>
        <li class="paquete__elemento">Pase a los dos dias</li>
        <li class="paquete__elemento">Acceso a las Grabaciones</li>
        <li class="paquete__elemento">Acceso a Talleres y Conferencias</li>
      </ul>

      <p class="paquete__precio">$49</p>

      
      <div
        class="paypal-button"
        data-paquete-id="2"
        data-token="<?php echo $token; ?>"
        id="paypal-virtual"></div>
    </div>
  </div>
</main>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $_ENV['PAYPAL_CLIENT_ID']; ?>&currency=USD&locale=es_AR&components=buttons"></script>

<script>
  (function() {
    const containers = document.querySelectorAll('.paypal-button');
    if (!containers.length) return;

    function mostrarPagoOk(container, texto = '✅ Pago completado') {
      // ocultar el botón
      container.style.display = 'none';

      // mostrar mensaje
      const msg = document.createElement('div');
      msg.className = 'paypal-ok';
      msg.textContent = texto;

      // insertarlo cerca del botón
      container.parentElement.appendChild(msg);
    }

    containers.forEach((container) => {
      const paqueteId = Number(container.dataset.paqueteId);
      const token = container.dataset.token;

      paypal.Buttons({
        createOrder: async () => {
          const res = await fetch('/finalizar-registro/paypal', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              paquete_id: paqueteId,
              token: token
            })
          });

          const raw = await res.text();
          if (!res.ok) {
            console.error('CREATE status:', res.status);
            console.error('CREATE body:', raw);
            throw new Error(raw || 'Error creando orden');
          }

          const data = JSON.parse(raw);
          if (!data.id) throw new Error('Backend no devolvió orderID');

          return data.id; // PayPal orderID
        },

        onApprove: async (data) => {
          const res = await fetch('/finalizar-registro/paypal-capturar', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              orderID: data.orderID,
              token: token,
              paquete_id: paqueteId
            })
          });

          const raw = await res.text();
          if (!res.ok) throw new Error(raw || 'Error capturando');

          const result = JSON.parse(raw);

          mostrarPagoOk(container, `✅ Pago completado. ID: ${result.pago_id}`);

          setTimeout(() => {
            window.location.href = '/finalizar-registro/conferencias';
          }, 2000);
        },

        onError: (err) => {
          console.error(err);
          alert('Hubo un error con PayPal. Revisá la consola.');
        }
      }).render('#' + container.id);

    });
  })();
</script>