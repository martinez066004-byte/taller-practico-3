<?php

require_once 'db.php'; // usa las mismas conexiones local + remota

echo "<h2>🔁 Sincronización inversa desde VM → Local</h2>";

if (!$mysqli_remote) {
    die("<p>❌ No se pudo conectar al servidor remoto (VM). Verifica conexión.</p>");
}

$tablas = ['suppliers', 'products', 'sales', 'sale_items', 'users'];
$errores = 0;

foreach ($tablas as $tabla) {
    echo "<h3>📦 Sincronizando tabla: $tabla</h3>";

    // Obtenemos todos los registros remotos
    $resRemote = $mysqli_remote->query("SELECT * FROM $tabla");
    if (!$resRemote) {
        echo "<p>⚠️ Error leyendo $tabla en remoto: " . $mysqli_remote->error . "</p>";
        $errores++;
        continue;
    }

    while ($row = $resRemote->fetch_assoc()) {
        // Verificar si ya existe localmente
        $id = $row['id'];
        $resLocal = $mysqli->query("SELECT * FROM $tabla WHERE id = $id");
        if ($resLocal && $resLocal->num_rows > 0) {
            // Actualizar si hay diferencias (por fecha o valores)
            $cols = [];
            foreach ($row as $col => $val) {
                if ($col === 'id') continue;
                $val = $mysqli->real_escape_string($val);
                $cols[] = "`$col`='$val'";
            }
            $updateSQL = "UPDATE $tabla SET " . implode(',', $cols) . " WHERE id=$id";
            if (!$mysqli->query($updateSQL)) {
                echo "<p>⚠️ Error actualizando ID $id en $tabla: " . $mysqli->error . "</p>";
            }
        } else {
            // Insertar nuevo registro
            $cols = implode(",", array_map(fn($c) => "`$c`", array_keys($row)));
            $vals = implode(",", array_map(fn($v) => "'" . $mysqli->real_escape_string($v) . "'", array_values($row)));
            $insertSQL = "INSERT INTO $tabla ($cols) VALUES ($vals)";
            if (!$mysqli->query($insertSQL)) {
                echo "<p>⚠️ Error insertando ID $id en $tabla: " . $mysqli->error . "</p>";
            }
        }
    }
}

if ($errores === 0) {
    echo "<p>✅ Sincronización completada correctamente.</p>";
} else {
    echo "<p>⚠️ Sincronización finalizada con algunos errores.</p>";
}
?>
