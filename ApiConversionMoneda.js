document.addEventListener("DOMContentLoaded", () => {
    const apiURL = "https://v6.exchangerate-api.com/v6/12d7b905e7ba22430f18afe3/pair/MXN/USD"; //Aqui esta es la mia uriel, pero sino funciona pon la key de tu cuenta

    
    const preciosDolares = document.querySelectorAll(".precio-dolares");

    //Solicitud pa la api
    fetch(apiURL)
        .then((response) => response.json())
        .then((data) => {
            const conversionRate = data.conversion_rate;

            preciosDolares.forEach((elemento) => {
                const precioPesos = parseFloat(elemento.dataset.precio);
                const precioUSD = (precioPesos * conversionRate).toFixed(2);
                elemento.textContent = `USD: $${precioUSD}`;
            });
        })
       
});
