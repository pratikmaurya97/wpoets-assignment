<?php
include 'db.php';

// Initialize an array to hold sections
$sections = [];

// Prepare the query for fetching tabs (Make sure 'icon_url' is selected)
$query = "SELECT id, tab_name, icon_url FROM tabs";  
$result = mysqli_query($conn, $query);

// Check if the query ran successfully
if (!$result) {
    die('Error fetching tabs: ' . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $tab_id = $row['id'];
    $tab_name = $row['tab_name'];
    $icon_url = !empty($row['icon_url']) ? $row['icon_url'] : 'default-icon.png'; // Fallback for missing icons

    // Prepare the query for fetching slides for each tab
    $slidesQuery = "SELECT title, subtitle, button_text, image_url FROM slides WHERE tab_id = ?";
    $stmt = mysqli_prepare($conn, $slidesQuery);

    // Check if the prepare statement was successful
    if (!$stmt) {
        die('Error preparing statement for slides: ' . mysqli_error($conn));
    }

    // Bind the tab_id parameter to the query
    mysqli_stmt_bind_param($stmt, "i", $tab_id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result of the query
    $slidesResult = mysqli_stmt_get_result($stmt);

    // Check if the query returned results
    if (!$slidesResult) {
        die('Error fetching slides: ' . mysqli_error($conn));
    }

    // Initialize an array to hold slides
    $slides = [];
    
    // Fetch each slide and add it to the slides array
    while ($slide = mysqli_fetch_assoc($slidesResult)) {
        $slides[] = [
            "title" => $slide['title'],
            "subtitle" => $slide['subtitle'],
            "button_text" => $slide['button_text'],
            "image_url" => !empty($slide['image_url']) ? $slide['image_url'] : 'default-image.jpg',  // Fallback for null image
        ];
    }

    // Add the tab and its slides to the sections array
    $sections[] = [
        "tab_name" => $tab_name,
        "icon_url" => $icon_url,  // Include icon URL for tabs
        "slides" => $slides,
    ];

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Return the sections data as JSON
echo json_encode($sections);

// Close the connection to the database
mysqli_close($conn);
?>
