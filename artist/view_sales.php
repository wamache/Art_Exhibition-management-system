<?php
// Start the session
session_start();

// Ensure the user is logged in and is an artist
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'artist') {
    header("Location: /project/art_exhibition/artist/login.php"); // Redirect to login if not logged in or not an artist
    exit;
}

// Include database configuration
include '../config/db.php';

// Get the artist's ID from the session
$artist_id = $_SESSION['user']['id'];

// Prepare and execute the query to fetch ticket sales
$stmt = $conn->prepare("
    SELECT exhibitions.title AS exhibition, COUNT(sales.id) AS tickets_sold
    FROM sales
    JOIN tickets ON sales.ticket_id = tickets.id
    JOIN exhibition_artworks ea ON tickets.exhibition_id = ea.exhibition_id
    JOIN artworks ON ea.artwork_id = artworks.id
    WHERE artworks.artist_id = ?
    GROUP BY exhibitions.id
");

// Bind the artist ID to the prepared statement
$stmt->bind_param("i", $artist_id);

// Execute the statement
$stmt->execute();

// Get the result of the query
$sales = $stmt->get_result();

// Check if the artist has any sales records
if ($sales->num_rows > 0) {
    echo "<h2>Ticket Sales for My Exhibitions</h2>";
    echo "<table border='1'>
            <tr><th>Exhibition</th><th>Tickets Sold</th></tr>";
    
    // Loop through and display the sales data
    while ($row = $sales->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['exhibition']) . "</td>
                <td>" . $row['tickets_sold'] . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No sales data found for your exhibitions.</p>";
}

// Close the statement
$stmt->close();
?>
