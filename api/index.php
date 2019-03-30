<?php
// --- BIBLIOTECAS
include_once "definitions.php";
include_once "songs.php";

// --- INTERFACE

// usuário

if(isset($_GET["song_create"])) {
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $song = urlencode($_POST["song"]);

    $song_id = song_create($name, $song);
    if($song_id) {
        echo $song_id;
    } else {
        echo "err";
    }
}

else if(isset($_GET["song_get"])) {
    $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);

    $song = song_get($id);
    $song["song"] = urldecode($song["song"]);

    if($song) {
        echo json_encode($song);
    } else {
        echo "err";
    }
}

else {
    continue_request(BACK_RESPONSE, "err=Invalid request.");
}