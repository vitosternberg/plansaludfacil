<?php
require_once 'config.php';
$conn = connect_db_simple();
if (!$conn) {
    die(json_encode(["error" => "No DB connection"]));
}
$res = $conn->query("SELECT * FROM procesar_formularios ORDER BY id_formulario_tipo DESC LIMIT 5");
if (!$res) {
    die(json_encode(["error" => $conn->error]));
}
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>
