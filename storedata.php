<?php

// MySQL credentials
$servername = "localhost";
$username = "prabinhudeo";
$password = "hudeop";
$dbname = "weather_webapp";

// Function to retrieve and display weather data
function displayWeatherData($locationName, $conn) {
    // Prepare the SQL statement to retrieve the 7 most recent historical weather data for the given location
    $sql = "SELECT * FROM weather_data
            WHERE location = ? 
            ORDER BY date DESC
            LIMIT 7";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter
    $stmt->bind_param("s", $locationName);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    //Check if there are any results
    if ($result->num_rows > 0) {
        // Get the location from the first row since it's the same for all rows
        $row = $result->fetch_assoc();
        $location = $row['location'];
        
        // Generate HTML content for historical weather data
        $htmlContent = '<div id="ph-history">';
        $htmlContent .= '<p class="ph-text-vertical">' . $location . '</p>';
        
        do {
            $htmlContent .= '<div class="ph-history__item"><p>' . date('Y-m-d', strtotime($row['date'])) . '</p>';
            $htmlContent .= '<p id="weatherCondition">' . $row['weather_main'] . '</p>';
            $htmlContent .= '<p>' . $row['temperature'] . '°C</p>';
            $htmlContent .= '<p>' . $row['humidity'] . '%</p>';
            $htmlContent .= '<p>' . $row['pressure'] . ' hPa</p>';
            $htmlContent .= '<p>' . $row['wind_speed'] . ' m/s</p>';
            $htmlContent .= '</div>';
        } while ($row = $result->fetch_assoc());
    
        $htmlContent .= '</div>';
    
        echo $htmlContent;
    } else {
        // If no historical data found, display a message
        echo '<div id="ph-history">';
        echo '<p>No historical weather data found.</p>';
        echo '</div>';
    }
    

    // Close the statement
    $stmt->close();
}

// Function to insert current weather data into the database
function insertCurrentWeatherData($locationName, $conn) {
    // Get the current weather data from the JSON file
    $jsonData = file_get_contents('data.json');
    $currentWeatherData = json_decode($jsonData, true);

    // Extract data for insertion
    $date = date('Y-m-d', $currentWeatherData['dt']);
    $weatherMain = isset($currentWeatherData['weather'][0]['main']) ? $currentWeatherData['weather'][0]['main'] : '';
    $temperature = isset($currentWeatherData['main']['temp']) ? $currentWeatherData['main']['temp'] : '';
    $humidity = isset($currentWeatherData['main']['humidity']) ? $currentWeatherData['main']['humidity'] : '';
    $pressure = isset($currentWeatherData['main']['pressure']) ? $currentWeatherData['main']['pressure'] : '';
    $windSpeed = isset($currentWeatherData['wind']['speed']) ? $currentWeatherData['wind']['speed'] : '';

    // Prepare the SQL statement to check if the record already exists
    $checkSql = "SELECT id FROM weather_data WHERE location = ? AND date = ? LIMIT 1";

    // Prepare the statement
    $checkStmt = $conn->prepare($checkSql);

    // Bind parameters
    $checkStmt->bind_param("ss", $locationName, $date);

    // Execute the statement
    $checkStmt->execute();

    // Get the result
    $checkResult = $checkStmt->get_result();

    // Check if a record with the same location and date already exists
    if ($checkResult->num_rows > 0) {
        // Update the existing record
        $updateSql = "UPDATE weather_data SET weather_main = ?, temperature = ?, humidity = ?, pressure = ?, wind_speed = ? WHERE location = ? AND date = ?";

        // Prepare the statement
        $updateStmt = $conn->prepare($updateSql);

        // Bind parameters
        $updateStmt->bind_param("sssssss", $weatherMain, $temperature, $humidity, $pressure, $windSpeed, $locationName, $date);

        // Execute the statement to update data
        $updateStmt->execute();

        // Close the statement
        $updateStmt->close();
    } else {
        // Insert a new record if no existing record found
        $insertSql = "INSERT INTO weather_data (location, date, weather_main, temperature, humidity, pressure, wind_speed) VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Prepare the statement
        $insertStmt = $conn->prepare($insertSql);

        // Bind parameters
        $insertStmt->bind_param("sssssss", $locationName, $date, $weatherMain, $temperature, $humidity, $pressure, $windSpeed);

        // Execute the statement to insert data
        $insertStmt->execute();

        // Close the statement
        $insertStmt->close();
    }

    // Close the check statement
    $checkStmt->close();
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the JSON data from the request body
    $jsonData = file_get_contents('php://input');
    // Log the received JSON data (for debugging purposes)
    file_put_contents('received_data.log', $jsonData);

    // Update the data.json file with the received JSON data
    file_put_contents('data.json', $jsonData);

    // Connect to the MySQL server
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(array('status' => 'error', 'message' => "Connection failed: " . $conn->connect_error)));
    }

    // Get the location name from the JSON data
    $dataArray = json_decode($jsonData, true);
    $locationName = isset($dataArray['name']) ? $dataArray['name'] : '';

    // Check if the location name is not empty
    if (!empty($locationName)) {
        // Call the function to insert current weather data into the database
        insertCurrentWeatherData($locationName, $conn);
    }

    // Close the connection
    $conn->close();

    // Return a response (optional, but can be helpful for debugging)
    echo json_encode(array('status' => 'success', 'message' => 'Data updated successfully.'));
}

// Check if the request method is GET
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Connect to the MySQL server
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(array('status' => 'error', 'message' => "Connection failed: " . $conn->connect_error)));
    }

    // Get the location name from the JSON data
    $jsonData = file_get_contents('data.json');
    $dataArray = json_decode($jsonData, true);
    $locationName = isset($dataArray['name']) ? $dataArray['name'] : '';

    // Check if the location name is not empty
    if (!empty($locationName)) {
        // Call the function to insert current weather data into the database
        insertCurrentWeatherData($locationName, $conn);

        // Call the function to retrieve and display weather data
        displayWeatherData($locationName, $conn);
    } else {
        // If location name is empty, echo an error message
        echo json_encode(array('status' => 'error', 'message' => "Error: Location name not found in the JSON data."));
    }

    // Close the connection
    $conn->close();
}





?>







