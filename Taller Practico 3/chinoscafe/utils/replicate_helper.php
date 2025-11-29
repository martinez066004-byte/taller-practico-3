<?php
require_once __DIR__ . '/../db.php';

function replicate_query($query, $params = []) {
    global $mysqli_remote;
    if (!$mysqli_remote) {
        error_log("[REPLICATE] No hay conexión remota.");
        return false;
    }

    $stmt = $mysqli_remote->prepare($query);
    if (!$stmt) {
        error_log("[REPLICATE] Prepare error: " . $mysqli_remote->error . " -- SQL: $query");
        return false;
    }

    if (!empty($params)) {
        $types = "";
        $refs = [];
        foreach ($params as $p) {
            if (is_int($p)) $types .= "i";
            elseif (is_float($p)) $types .= "d";
            else $types .= "s";
        }
        $refs[] = $types;
        foreach ($params as $i => $val) {
            ${"param$i"} = $val;
            $refs[] = &${"param$i"};
        }
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    $ok = $stmt->execute();
    if (!$ok) {
        error_log("[REPLICATE] Execute error: " . $stmt->error . " -- SQL: $query");
    }
    $stmt->close();
    return $ok;
}
?>