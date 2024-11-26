<?php
include 'db.php'; // Archivo para la conexión a la base de datos

// Obtén los datos enviados por POST
$user_id = $_POST['user_id'];
$episode_number = $_POST['episode_number'];
$watched = $_POST['watched'] == 'true' ? 1 : 0;

// Actualiza el estado del episodio
$sql = "INSERT INTO user_episodes (user_id, episode_number, watched) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE watched = VALUES(watched)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $episode_number, $watched);

if ($stmt->execute()) {
    echo "Estado actualizado correctamente";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
