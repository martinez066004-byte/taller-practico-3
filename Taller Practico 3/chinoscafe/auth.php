<?php
require_once 'db.php';

function require_auth() {
    if (empty($_SESSION['user_id'])) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'];
        $_SESSION['flash'] = ['type'=>'warning','msg'=>'Debes iniciar sesión para acceder.'];
        header('Location: login.php');
        exit;
    }
}

function flash_set($type, $msg) {
    $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg];
}
function flash_get() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function current_user() {
    global $mysqli;
    if (empty($_SESSION['user_id'])) return null;
    static $cache = null;
    if ($cache !== null) return $cache;
    $id = intval($_SESSION['user_id']);
    $res = $mysqli->query("SELECT id, username, full_name FROM users WHERE id = $id LIMIT 1");
    if ($res && $res->num_rows) {
        $cache = $res->fetch_assoc();
        return $cache;
    }
    return null;
}