<?php // no me acuerdo si conecte esto con algo más asi que no lo estoy tocando...
require_once 'db.php';
require_once 'utils/replicate_helper.php';

echo "<h2>🔁 Prueba de replicación</h2>";

if (!$mysqli_remote) {
    echo "<p>❌ No se pudo conectar al servidor remoto (verifica IP y credenciales).</p>";
    exit;
}

// Prueba simple: replicar proveedores
$res = $mysqli->query("SELECT * FROM suppliers");
while ($row = $res->fetch_assoc()) {
    $sql = "INSERT INTO suppliers (id, name, phone, address, email) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                name=VALUES(name), phone=VALUES(phone), address=VALUES(address), email=VALUES(email)";
    replicate_query($sql, [
        $row['id'], $row['name'], $row['phone'], $row['address'], $row['email']
    ]);
}
echo "<p>✅ Datos replicados correctamente.</p>";
?>
