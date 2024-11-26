<?php
include 'db.php'; // Archivo para la conexión a la base de datos

// Verifica que los datos se reciban correctamente
if (!isset($_POST['user_id']) || !isset($_POST['episode_number']) || !isset($_POST['timecurrent'])) {
    die('Error: Datos incompletos');
}

$user_id = intval($_POST['user_id']);
$episode_number = intval($_POST['episode_number']);
$timecurrent = floatval($_POST['timecurrent']);

// Prepara la consulta SQL para insertar o actualizar
$sql = "INSERT INTO user_episodes (user_id, episode_number, watched, timecurrent) 
        VALUES (?, ?, 0, ?) 
        ON DUPLICATE KEY UPDATE timecurrent = ?";

// Prepara la declaración
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincula los parámetros
$stmt->bind_param("iidi", $user_id, $episode_number, $timecurrent, $timecurrent);

// Ejecuta la consulta
if ($stmt->execute()) {
    echo "Tiempo de reproducción guardado correctamente";
} else {
    echo "Error en la ejecución de la consulta: " . $stmt->error;
}

// Cierra la declaración y la conexión
$stmt->close();
$conn->close();
?>
