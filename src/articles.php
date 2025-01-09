<?php
include "connectionArticles.php";

try {
    // Create a new Database object
    $db = new Database('mydatabase.db');
} catch (Exception $e) {
    die('Could not connect to the database: ' . $e->getMessage());
}

// Define the HTML content
$_HTML = '<h1>Ola Mundo</h1>';
$_HTMLCOD = htmlspecialchars($_HTML);

// Prepare the SQL insert statement without specifying the id
$query = "INSERT INTO articlee (html, por, likes) VALUES (:html, :por, :likes)";

// Define the parameters
$params = array(
    'html' => $_HTMLCOD,
    'por' => 'NON',
    'likes' => '10'
);

try {
    // Execute the query using the Database class
    $db->query($query, $params);
    echo "Insert successful.";
    
    // Optionally, retrieve the last inserted id
    // $_lastId = $db->lastInsertRowID();
    // echo "Last inserted ID: " . $_lastId;
} catch (Exception $e) {
    die('Error inserting data: ' . $e->getMessage());
} finally {
    // Close the database connection
    $db->close();
}
?>
