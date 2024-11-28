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

// Validasi data yang diterima
if(
    !empty($data->first_name) &&
    !empty($data->last_name) &&
    !empty($data->email) &&
    !empty($data->mobile) &&
    !empty($data->username) &&
    !empty($data->password)
){
    // Cek apakah username sudah ada
    $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$data->username, $data->email]);
    
    if($check_stmt->rowCount() > 0){
        http_response_code(400);
        echo json_encode(array("message" => "Username atau email sudah terdaftar."));
        exit();
    }
    
    // Query untuk insert data
    $query = "INSERT INTO users 
              SET first_name = :first_name,
                  last_name = :last_name,
                  email = :email,
                  mobile = :mobile,
                  username = :username,
                  password = :password";

    try {
        $stmt = $db->prepare($query);
        
        // Sanitasi input
        $first_name = htmlspecialchars(strip_tags($data->first_name));
        $last_name = htmlspecialchars(strip_tags($data->last_name));
        $email = htmlspecialchars(strip_tags($data->email));
        $mobile = htmlspecialchars(strip_tags($data->mobile));
        $username = htmlspecialchars(strip_tags($data->username));
        $password = password_hash($data->password, PASSWORD_DEFAULT);
        
        // Bind parameter
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":mobile", $mobile);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        
        // Execute query
        if($stmt->execute()){
            http_response_code(201);
            echo json_encode(array(
                "message" => "Registrasi berhasil.",
                "user_id" => $db->lastInsertId()
            ));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Registrasi gagal."));
        }
    } catch(PDOException $e) {
        http_response_code(503);
        echo json_encode(array(
            "message" => "Registrasi gagal.",
            "error" => $e->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Data tidak lengkap."));
}
?>