<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// MySQL credentials
$servername = "localhost";
$username = "prabinhudeo";
$password = "hudeop";
$dbname = "weather_webapp";

// Function to retrieve and display weather data
function displayWeatherData($locationName, $conn) {
    // Prepare the SQL statement to retrieve the 7 most recent weather data for the given location
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
        // Initialize an array to keep track of displayed dates
        $displayedDates = array();

        // Output the data
        while ($row = $result->fetch_assoc()) {
            $date = date('Y-m-d', strtotime($row['date'])); // Convert the date to Y-m-d format
            // Check if the date has already been displayed
            if (!in_array($date, $displayedDates)) {
                // Add the date to the displayedDates array
                $displayedDates[] = $date;

                echo "Date: " . $row['date'] . "<br>";
                echo "Location Name: " . $row['location'] . "<br>";
                echo "Weather Main: " . $row['weather_main'] . "<br>";
                echo "Temperature: " . $row['temperature'] . "<br>";
                echo "Humidity: " . $row['humidity'] . "<br>";
                echo "Pressure: " . $row['pressure'] . "<br>";
                echo "Wind Speed: " . $row['wind_speed'] . "<br>";
                echo "<hr>";
            }
        }
    } else {
        echo "No data found for the location: " . $locationName;
    }

    // Close the statement
    $stmt->close();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the JSON data from the POST request body
    $jsonData = file_get_contents('php://input');

    try {
        // Check if JSON data exists
        if (!empty($jsonData)) {
            // Write the JSON data to the data.json file
            file_put_contents('data.json', $jsonData);

            // Return a response to indicate the data was successfully updated
            $response = array('status' => 'success', 'message' => 'Data updated successfully');
            echo json_encode($response);
        } else {
            // If JSON data is missing in the request, throw an exception
            throw new Exception('Error: JSON data not found in the request.');
        }
    } catch (Exception $e) {
        // If an exception is caught, echo the error message
        echo $e->getMessage();
    }
}

// Get the location name from the JSON data
$jsonData = file_get_contents('data.json');
$dataArray = json_decode($jsonData, true);
$locationName = isset($dataArray['name']) ? $dataArray['name'] : '';

// Check if the location name is not empty
if (!empty($locationName)) {
    // Connect to the MySQL server
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Call the function to retrieve and display weather data
    displayWeatherData($locationName, $conn);

    // Close the connection
    $conn->close();
} else {
    // If location name is empty, echo an error message
    echo "Error: Location name not found in the JSON data.";
}
?>
