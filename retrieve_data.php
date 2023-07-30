<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// MySQL credentials
$servername = "localhost";
$username = "prabinhudeo";
$password = "hudeop";
$dbname = "weather_webapp";

// Function to retrieve and display weather data
function getCurrentWeatherData() {
    // Get the current weather data from the JSON file
    $jsonData = file_get_contents('data.json');
    return json_decode($jsonData, true);
}

// Function to retrieve historical weather data
function getHistoricalWeatherData($locationName, $conn) {
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

    // Check if there are any results
    if ($result->num_rows > 0) {
        // Initialize an array to store historical weather data
        $historyWeatherData = array();
        $displayedDates = array();

        // Fetch and store the historical data
        while ($row = $result->fetch_assoc()) {
            $date = date('Y-m-d', strtotime($row['date']));

            // Check if the date has already been displayed
            if (!in_array($date, $displayedDates)) {
                // Add the date to the displayedDates array
                $displayedDates[] = $date;

                // Store the historical data in the array
                $historyWeatherData[] = array(
                    'date' => $row['date'],
                    'location' => $row['location'],
                    'weather_main' => $row['weather_main'],
                    'temperature' => $row['temperature'],
                    'humidity' => $row['humidity'],
                    'pressure' => $row['pressure'],
                    'wind_speed' => $row['wind_speed']
                );
            }
        }

        return $historyWeatherData;
    } else {
        return array(); // Return an empty array if no historical data found
    }

    // Close the statement
    $stmt->close();
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
        // Call the function to retrieve historical weather data
        $historicalWeatherData = getHistoricalWeatherData($locationName, $conn);

        // Prepare the response data as an array
        $response = array(
            'historicalWeatherData' => $historicalWeatherData
        );

        // Close the connection
        $conn->close();

        // Write historical weather data to the history_data.json file
        $historicalDataJson = json_encode($historicalWeatherData);
        file_put_contents('history_data.json', $historicalDataJson);

        // Return the JSON response
        echo json_encode($response);
    } else {
        // If location name is empty, echo an error message
        echo json_encode(array('status' => 'error', 'message' => "Error: Location name not found in the JSON data."));
    }
}
