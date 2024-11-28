<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    $query = "DELETE FROM tasks WHERE id = ?";
    $stmt = $db->prepare($query);

    if($stmt->execute([$data->id])) {
        http_response_code(200);
        echo json_encode(array("message" => "Todo berhasil dihapus."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Gagal menghapus todo."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?>