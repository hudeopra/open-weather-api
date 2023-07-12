


const apikey = "eb00a5c5306a122e858cd765a9c56429";
var currentLocation = `east+ayrshire`;
let url = `https://api.openweathermap.org/data/2.5/weather?&units=metric`;
// feching the data from the API URL
async function getdate() {
    const date = new Date()
    var day = date.getDate()
    var month = date.getMonth() + 1

    function getMonthName(month) {
        if (month === 1) {
            return "Jan";
        } else if (month === 2) {
            return "Feb";
        } else if (month === 3) {
            return "Mar";
        } else if (month === 4) {
            return "Apr";
        } else if (month === 5) {
            return "May";
        } else if (month === 6) {
            return "Jun";
        } else if (month === 7) {
            return "Jul";
        } else if (month === 8) {
            return "Aug";
        } else if (month === 9) {
            return "Sep";
        } else if (month === 10) {
            return "Oct";
        } else if (month === 11) {
            return "Nov";
        } else if (month === 12) {
            return "Dec";
        } else {
            return "Invalid month";
        }
    }
    var year = date.getFullYear()
    document.getElementById("ph-date").innerHTML = ` ${day}${dayFormat(day)} ${getMonthName(month)} ${year}`;

}
function dayFormat(day) {
    if (day === 1 || day === 21 || day === 31) {
        return "st";
    } else if (day === 2 || day === 22) {
        return "nd";
    } else if (day === 3 || day === 23) {
        return "rd";
    } else {
        return "th";
    }
}
getdate()
async function weatherCheck(city) {
    const response = await fetch(url + `&appid=${apikey}` + `&q=` + city);
    var data = await response.json();
    console.log(data);
    console.log(`Current Location: ${data.name}`);
    document.getElementById("ph-current-weather").innerHTML = `${data.weather[0].main}`

    document.getElementById("ph-current-location").innerHTML = `${data.name}`
    document.getElementById("ph-current-temp").innerHTML = `${data.main.temp} `

    document.getElementById("humidity").innerHTML = `${data.main.humidity} `
    document.getElementById("air-pressure").innerHTML = `${data.main.pressure} `
    document.getElementById("wind-speed").innerHTML = `${data.wind.speed} `
}
weatherCheck(currentLocation)

let searchCity;

const searchForm = document.forms.search;

function validateForm(e) {
    e.preventDefault();
    searchCity = searchForm.cityName.value;
    console.log(searchCity);
    try {
        if (!searchCity) throw "The search box is empty";
    } catch (err) {
        alert(err);
    }
    currentLocation = searchCity
    weatherCheck(currentLocation)
}

searchForm.addEventListener("submit", validateForm);

// Access searchCity variable outside of the function
console.log(searchCity);
// weatherCheck(searchCity)
// fetch(url)
//     // .then is a promice which flows only sucessful events
//     .then((response) => response.json())
//     .then((data) => {
//         console.log(data);
//         console.log(data.name);
//         document.getElementById("ph-current-weather").innerHTML = `${ data.weather[0].main } `

//         document.getElementById("ph-current-location").innerHTML = `${ data.name } `
//         document.getElementById("ph-current-temp").innerHTML = `${ data.main.temp } `

//         document.getElementById("humidity").innerHTML = `${ data.main.humidity } `
//         document.getElementById("air-pressure").innerHTML = `${ data.main.pressure } `
//         document.getElementById("ph-current-temp").innerHTML = `${ data.main.temp } `
//         document.getElementById("wind-speed").innerHTML = `${ data.wind.speed } `

//     });

// no date
// no chance of rain
// no icon update
