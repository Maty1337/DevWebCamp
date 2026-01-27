if(document.querySelector('#mapa')) {

    const lat = -32.8879053;
    const lng = -68.8725107;
    const zoom = 16;

    const map = L.map('mapa').setView([lat, lng], zoom);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

L.marker([lat, lng]).addTo(map)
    .bindPopup(`
            <h2 class="mapa__heading">DevWebCamp 2024</h2>
            <p class="mapa__texto">Te esperamos en DevWebCamp</p>
        `)
    .openPopup();
}