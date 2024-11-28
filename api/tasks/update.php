<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    $query = "UPDATE tasks SET ";
    $params = array();
    
    if(isset($data->title)) {
        $query .= "title=:title, ";
        $params[':title'] = $data->title;
    }
    
    if(isset($data->status)) {
        $query .= "status=:status, ";
        $params[':status'] = $data->status;
    }
    
    $query = rtrim($query, ", ");
    $query .= " WHERE id=:id";
    $params[':id'] = $data->id;

    $stmt = $db->prepare($query);

    if($stmt->execute($params)) {
        http_response_code(200);
        echo json_encode(array("message" => "Todo berhasil diupdate."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Gagal mengupdate todo."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?>