<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->password)) {
    $query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->username]);
    
    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(password_verify($data->password, $row['password'])) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login berhasil.",
                "user_id" => $row['id'],
                "username" => $row['username']
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Password salah."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Username tidak ditemukan."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?>