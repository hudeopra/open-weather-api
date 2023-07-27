// Name: Prabin Maharjan
// UID: 2358823

const apikey = "eb00a5c5306a122e858cd765a9c56429";
var currentLocation = `east+ayrshire`;
let url = `https://api.openweathermap.org/data/2.5/weather?&units=metric`;
const weatherIcon = document.getElementById("ph-main__weather-icon");



// feching the data from the API URL using async function
async function getdate() {
    // Date() is a built in JS function and storing its value in date  variable
    const date = new Date();
    // assigning the value of getDate(), getMonth() and getFullYear() methods to the variables
    var day = date.getDate();
    // getMonth() method returns the month component of a date as a zero-based value so adding +1 to it 
    var month = date.getMonth() + 1;
    var year = date.getFullYear();

    // getMonthName function gets the numeric value from the month variable and converts it into and array
    function getMonthName(month) {
        // conditional statements
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
    // HTML element with the ID "ph-date" is being updated with a formatted date string.
    document.getElementById("ph-date").innerHTML = ` ${day}${dayFormat(day)} ${getMonthName(month)}, ${year}`;

}
// dayFormat(day) function determines what value is stored in day variable and returns appropriate ordinal suffix
function dayFormat(day) {
    // conditional statements
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
// calling getdate() function
getdate()

// Function to update weather data and display on the webpage
function updateWeatherData(data) {
    document.getElementById("ph-current-weather").innerHTML = data.weather[0].main;
    document.getElementById("ph-current-location").innerHTML = data.name;
    document.getElementById("ph-current-temp").innerHTML = `${data.main.temp} `;
    document.getElementById("humidity").innerHTML = `${data.main.humidity} `;
    document.getElementById("air-pressure").innerHTML = `${data.main.pressure} `;
    document.getElementById("wind-speed").innerHTML = `${data.wind.speed} `;


    // conditional statements 
    // based on the data received from API source URL in the image tag with ID "ph-main__weather-icon" being updated or changed
    if (data.weather[0].main == "Clear" || data.weather[0].main == "Clear Sky") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/clear.png";
    } else if (data.weather[0].main == "Clouds" || data.weather[0].main == "Few Clouds" || data.weather[0].main == "Scattered Clouds" || data.weather[0].main == "Broken Clouds") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/cloudy.png";
    } else if (data.weather[0].main == "Rain" || data.weather[0].main == "Shower Rain" || data.weather[0].main == "Drizzle") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/rainy.png";
    } else if (data.weather[0].main == "Thunderstorm") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/thunderstorm.png";
    } else if (data.weather[0].main == "Snow") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/snow.png";
    } else if (data.weather[0].main == "Mist" || data.weather[0].main == "Fog") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/mist.png";
    } else if (data.weather[0].main == "Haze" || data.weather[0].main == "Smoke") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/haze.png";
    } else if (data.weather[0].main == "Dust" || data.weather[0].main == "Sand") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/dust.png";
    } else if (data.weather[0].main == "Ash") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/ash.png";
    } else if (data.weather[0].main == "Tornado") {
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/tornado.png";
    } else {
        // Default image if the weather condition is not recognized
        weatherIcon.src = "https://hudeopra.github.io/open-weather-api/img/icon/default.png";
    }

}

// Function to fetch weather data from the API
async function weatherCheck(city) {
    try {
        const response = await fetch(url + `&appid=${apikey}` + `&q=` + city);
        const data = await response.json();
        console.log(data)
        if (data.cod === "404") {
            alert("City not found");
            return;
        }

        updateWeatherData(data);

        // Convert the weatherData object to JSON string
        const jsonData = JSON.stringify(data);

        // Log the JSON data in the console (optional)
        console.log(jsonData);

        // Store the JSON data on the server using AJAX
        await storeDataOnServer(jsonData);
    } catch (error) {
        console.error('Error:', error);
    }
}
// Function to store the JSON data on the server using AJAX
async function storeDataOnServer(jsonData) {
    try {
        // Validate JSON data
        const parsedData = JSON.parse(jsonData);
        if (!parsedData) {
            throw "Invalid JSON data";
        }

        // Send an AJAX POST request to the PHP script to store the data
        const response = await fetch('http://localhost/weather/storedata.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: jsonData // Send JSON data directly without wrapping it inside 'jsonData'
        });

        // Check the response status
        if (!response.ok) {
            throw "Error storing data on the server";
        }

        // Parse the response
        const result = await response.json();

        // Log the result in the console (optional)
        console.log(result);

        // Update the data.json file with the latest JSON data
        await updateDataFile(jsonData); // Call the function to update the data.json file
    } catch (error) {
        console.error('Error:', error);
    }
}

// Function to update the data.json file with the latest JSON data
async function updateDataFile(jsonData) {
    try {
        // Send an AJAX POST request to the PHP script to update the data.json file
        const response = await fetch('http://localhost/weather/updateDataFile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: jsonData
        });

        // Check the response status
        if (!response.ok) {
            throw "Error updating data.json file";
        }

        // Parse the response
        const result = await response.json();

        // Log the result in the console (optional)
        console.log(result);
    } catch (error) {
        console.error('Error:', error);
    }
}


// Function to execute when the page loads
async function onPageLoad() {
    const searchForm = document.forms.search;

    // Adds an event listener to the form element with the ID "search" for the "submit" event.
    // When the form is submitted, the validateForm function is called.
    searchForm.addEventListener("submit", validateForm);

    // Adds a click event listener to the entire document (page) using jQuery's $(document).on() function.
    // The event listener is triggered when a click event occurs on an element with the ID "ph-search-trigger".
    // It is an anonymous function.
    $(document).on('click', '#ph-search-trigger', function () {
        // toggle class named "active" every time the function is triggered
        $('#ph-search-trigger').toggleClass('active');
    });

    await weatherCheck(currentLocation);
}

// Declare the searchForm variable outside the onPageLoad function
const searchForm = document.forms.search;

// Function to validate the search form
function validateForm(e) {
    // Prevents the default form submission behavior
    e.preventDefault();

    // Retrieves the value entered in the input field with the name "cityName"
    const searchCity = searchForm.cityName.value;

    try {
        // Checks if the searchCity variable is empty
        if (!searchCity) throw "The search box is empty";
    } catch (err) {
        // Displays an alert message if the searchCity variable is empty
        alert(err);
        return;
    }

    // Calls the weatherCheck function with the updated searchCity value
    weatherCheck(searchCity);
}

// Execute onPageLoad function when the page finishes loading
window.addEventListener('load', onPageLoad);

// Adds an event listener to the form element with the ID "search" for the "submit" event.
// When the form is submitted, the validateForm function is called.
searchForm.addEventListener("submit", validateForm);


// Adds a click event listener to the entire document (page) using jQuery's $(document).on() function.
// The event listener is triggered when a click event occurs on an element with the ID "ph-search-trigger".
// it is an anonymous function
$(document).on('click', '.ph-history #ph-search-trigger', function () {
    // toggle class named "active" everytime the function is triggred 
    $('#ph-search-trigger').toggleClass('active');
});


