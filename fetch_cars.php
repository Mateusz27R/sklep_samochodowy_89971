<?php
include 'db_connect.php';

header('Content-Type: application/json');

// Domyślnie sortowanie po cenie rosnąco
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'priceAsc';

switch ($sortOption) {
    case 'priceAsc':
        $orderBy = 'price ASC';
        break;
    case 'priceDesc':
        $orderBy = 'price DESC';
        break;
    default:
        $orderBy = 'price ASC';
}

// Zapytanie SQL z dynamicznym sortowaniem
$sql = "SELECT * FROM cars ORDER BY $orderBy";
$result = $conn->query($sql);

$cars = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

echo json_encode($cars);
?>
