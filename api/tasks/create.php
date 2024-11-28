<?php
include_once '../config/database.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->user_id) && !empty($data->title)) {
    $query = "INSERT INTO tasks SET user_id=:user_id, title=:title";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":user_id", $data->user_id);
    $stmt->bindParam(":title", $data->title);

    try {
        if($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array(
                "message" => "Todo berhasil dibuat.",
                "id" => $db->lastInsertId()
            ));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Gagal membuat todo."));
        }
    } catch(PDOException $e) {
        http_response_code(503);
        echo json_encode(array(
            "message" => "Gagal membuat todo.",
            "error" => $e->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?>