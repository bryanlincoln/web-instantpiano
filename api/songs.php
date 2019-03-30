<?php

function song_create($name, $song) {
    if(!db_insert(
        "songs",
        array("name", "song"),
        array($name, $song)
    )) {
        return false;
    }

    $id = db_select("songs", array("id"), "1", false, "id DESC LIMIT 1")->fetch_assoc()["id"];
    return $id;
}

function song_get($id) {
    $song = db_select("songs", array("*"), "id=".$id)->fetch_assoc();
    if(!$song) return false;
    return $song;
}