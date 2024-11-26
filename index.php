<?php
include 'db.php'; // Archivo para la conexión a la base de datos

// Obtén el ID del usuario (esto podría provenir de una sesión o autenticación)
$user_id = 1; // Ejemplo de ID de usuario

// Consulta para obtener el estado de los episodios
$sql = "SELECT episode_number, watched, timecurrent FROM user_episodes WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Guarda el estado de los episodios en un array
$episodes_watched = [];
while ($row = $result->fetch_assoc()) {
    $episodes_watched[$row['episode_number']] = [
        'watched' => $row['watched'],
        'timecurrent' => $row['timecurrent']
    ];
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PePeVFlix - Temporada 1</title>
    <link rel="icon" type="image/jpg" href="p.png" />
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <img src="pepevflix.png" alt="Logo">
    <h1>Chicago PD - Temporada 1</h1>

    <!-- Contenedor de carga -->
    <div class="loading-container">
        <img src="loading.gif" alt="Cargando...">
    </div>

    <!-- Cuadrícula de episodios -->
    <div class="episode-grid">
        <!-- Episodios aquí -->
        <?php for ($i = 1; $i <= 19; $i++): ?>
            <div class="episode" data-video-src="T10/T10_E<?php echo $i; ?>.mp4" data-episode-number="<?php echo $i; ?>"
                data-thumbnail-time="20">
                <img src="" alt="Episode <?php echo $i; ?> Thumbnail">
                <img class="watched-overlay"
                    style="display: <?php echo isset($episodes_watched[$i]) && $episodes_watched[$i]['watched'] ? 'block' : 'none'; ?>;">
                <input type="checkbox" class="episode-checkbox" <?php echo isset($episodes_watched[$i]) && $episodes_watched[$i]['watched'] ? 'checked' : ''; ?>>
                <div class="episode-popup">
                    <div class="popup-content">
                        <button class="close-btn">Cerrar</button>
                        <video controls>
                            <source src="T10/T10_E<?php echo $i; ?>.mp4" type="video/mp4">
                            Tu navegador no soporta el elemento de video.
                        </video>
                        <div class="video-controls">
                            <button class="seek-backward">-5s</button>
                            <button class="seek-forward">+5s</button>
                        </div>
                    </div>
                </div>
                <div class="episode-title">Capítulo <?php echo $i; ?></div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Botones de navegación -->
    <div class="nav-btn prev">&laquo; Anterior</div>
    <div class="nav-btn next">Siguiente &raquo;</div>

    <script src="scripts.js"></script>
    <script>
        // Pasa los datos de PHP a JavaScript
        const episodes_watched = <?php echo json_encode($episodes_watched); ?>;

        // Configura el volumen de todos los videos al 25%
        document.addEventListener('DOMContentLoaded', function () {
            const videos = document.querySelectorAll('video');
            videos.forEach(video => {
                video.volume = 0.25; // Establece el volumen al 25%
            });
        });
    </script>
</body>

</html>