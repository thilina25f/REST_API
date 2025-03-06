<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");


// INclude database connection
require 'config.php';

// Get the HTTP method
$method = $_SERVER['REQUEST_METHOD'];

//Get the user ID from the URL (if exists)
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
// if($id == 0){
//     $id = -1;
// }

// Handle API requests based on the HTTP method

switch($method){
    case 'GET':
        if($id){
            // Return specific user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            echo json_encode($result ?: ["message" => "User not found"]);
        }else{
            $result = $conn->query("SELECT * FROM users");
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
        }
        break;

        case 'POST':
            // Create a new user
            $data = json_decode(file_get_contents('php://input'), true);
            if(isset($data['name'], $data['email'])){
                $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
                $stmt->bind_param("ss", $data['name'], $data['email']);
                $stmt->execute();
                echo json_encode(["message" => "User created", "id" => $stmt->insert_id]);                
            }else{
                echo json_encode(["message" => "Invalid input"]);
            }
            break;

        case 'PUT':
            // Update on existing user
            if($id){
                $data = json_decode(file_get_contents('php://input'), true);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $data['name'], $data['email'], $id);
                $stmt->execute();
                echo json_encode(["message" => "User updated"]);            
            }else{
                echo json_encode(['message' => 'User ID is requird']);
            }
            break;

        case 'DELETE':
            // Delete a user
            if($id){
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                echo json_encode(['message' => 'User deleted']);
            }else{
                echo json_encode(['message' => 'User ID is requird']);
            }
            break;

        case 'OPTIONS':
            header("Allow: GET, POST, PUT, DELETE, OPTIONS");
            break;

        default:
            echo json_encode(['message' => 'Method not allowed']);
            break;
        
}

$conn->close();