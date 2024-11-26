<?php
include 'db.php'; // Archivo para la conexión a la base de datos

// Verifica que los datos se reciban correctamente
if (!isset($_POST['user_id']) || !isset($_POST['episode_number'])) {
    echo json_encode(['timecurrent' => 0]);
    exit;
}

$user_id = intval($_POST['user_id']);
$episode_number = intval($_POST['episode_number']);

// Consulta para obtener el tiempo guardado
$sql = "SELECT timecurrent FROM user_episodes WHERE user_id = ? AND episode_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $episode_number);
$stmt->execute();
$stmt->bind_result($timecurrent);
$stmt->fetch();
$stmt->close();
$conn->close();

// Retorna el tiempo guardado en formato JSON
echo json_encode(['timecurrent' => $timecurrent ?? 0]);
?>
