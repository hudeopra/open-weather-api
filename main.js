


const apikey = "eb00a5c5306a122e858cd765a9c56429";
let url = `https://api.openweathermap.org/data/2.5/weather?q=east+ayrshire&appid=${apikey}&units=metric`;
// feching the data from the API URL

fetch(url)
    // .then is a promice which flows only sucessful events
    .then((response) => response.json())
    .then((data) => {
        console.log(data);
        console.log(data.name);
        document.getElementById("ph-current-weather").innerHTML = `${data.weather[0].main}`

        document.getElementById("ph-current-location").innerHTML = `${data.name}`
        document.getElementById("ph-current-temp").innerHTML = `${data.main.temp}`

        document.getElementById("humidity").innerHTML = `${data.main.humidity}`
        document.getElementById("air-pressure").innerHTML = `${data.main.pressure}`
        document.getElementById("ph-current-temp").innerHTML = `${data.main.temp}`
        document.getElementById("wind-speed").innerHTML = `${data.wind.speed}`

    });

// no date
// no chance of rain
// no icon update
