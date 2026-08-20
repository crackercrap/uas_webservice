<?php
header("Content-Type: application/json; charset=utf-8");

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$headers = array_change_key_case(getallheaders(), CASE_LOWER);

function jsonResponse($code, $status, $message = "", $data = []) {
    http_response_code($code);
    $response = ['status' => $status];

    if ($message !== "") {
        $response['message'] = $message;
    }

    if (!empty($data)) {
        $response['data'] = $data;
    }

    echo json_encode($response);
    exit;
}

function requireHeaders($method, $headers) {
    if (($headers['authorization'] ?? "") !== "Bearer xyz123") {
        jsonResponse(401, "error", "Unauthorized: header ' Authorization: Bearer xyz123'");
    }

    if (stripos($headers['accept'] ?? "", "application/json") === false) {
        jsonResponse(406, "error", "Not Acceptable: accept application/json");
    }

    if (($method === "POST" || $method === "PUT") &&
        stripos($headers['content-type'] ?? "", "application/json") === false) {
        jsonResponse(415, "error", "Unsupported Media Type: 'content-type: application/json'");
    }
}

try {
    $conn = new mysqli("localhost", "root", "", "webserv");
    if ($conn->connect_error) {
        throw new mysqli_sql_exception($conn->connect_error);
    }
} catch (mysqli_sql_exception $e) {
    jsonResponse(500, "error", "Database Connection failed: " . $e->getMessage());
}

requireHeaders($method, $headers);

switch ($method) {
    case "GET":
        if (isset($_GET['id'])) {
            getDetailProvince($conn, $_GET['id']);
        } else {
            getAllProvince($conn);
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        createProvince($conn, $data);
        break;

    case "PUT":
        if (!isset($_GET['id'])) {
            jsonResponse(400, "error", "Missing 'id' parameter");
        }

        $data = json_decode(file_get_contents("php://input"), true);
        updateProvince($conn, $_GET['id'], $data);
        break;

    case "DELETE":
        if (!isset($_GET['id'])) {
            jsonResponse(400, "error", "Missing 'id' parameter");
        }

        deleteProvince($conn, $_GET['id']);
        break;

    default:
        jsonResponse(405, "error", "Method not allowed");
}

function getAllProvince($conn) {
    $query = $conn->query("SELECT * FROM province");

    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }

    jsonResponse(200, "success", "", $data);

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
}

function getDetailProvince($conn, $id) {
    $query = $conn->query("SELECT * FROM province WHERE province_id = '$id'");
    $data = $query->fetch_assoc();

    jsonResponse(200, "success", "", $data);

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
}

function createProvince($conn, $data) {
    if (!isset($data['name']) || trim((string) $data['name']) === "") {
        jsonResponse(400, "error", "Field 'name' is required");
        return;
    }
    
    $name = $data['name'];
    $conn->query("INSERT INTO province (province_name) VALUES ('$name')");

    jsonResponse(201, "success", "Province created successfully");
    
}

function updateProvince($conn, $id, $data) {
    if (!isset($data['name']) || trim((string) $data['name']) === "") {
        jsonResponse(400, "error", "Field 'name' is required");
        return;
    }

    $name = $data['name'];
    $conn->query("UPDATE province SET province_name = '$name' WHERE province_id = '$id'");
    jsonResponse(200, "success", "Province updated successfully");
    
    }

function deleteProvince($conn, $id) {
    $conn->query("DELETE FROM province WHERE province_id = '$id'");

    jsonResponse(200, "success", "Province deleted successfully");
}
?>