<?php
require_once __DIR__ . '/replicate_helper.php';

function insert_or_update_remote($table, $rowAssoc) {
    $cols = array_keys($rowAssoc);
    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    $colList = implode(',', $cols);
    $updates = implode(',', array_map(function($c){ return "$c=VALUES($c)"; }, $cols));
    $sql = "INSERT INTO $table ($colList) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updates";
    $params = array_values($rowAssoc);
    return replicate_query($sql, $params);
}
?>