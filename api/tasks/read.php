<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();

$query = "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$user_id]);

if($stmt->rowCount() > 0) {
    $todos_arr = array();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        $todo_item = array(
            "id" => $id,
            "title" => $title,
            "status" => $status,
            "created_at" => $created_at
        );

        array_push($todos_arr, $todo_item);
    }

    http_response_code(200);
    echo json_encode($todos_arr);
} else {
    http_response_code(200);
    echo json_encode(array());
}
?>