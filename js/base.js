$(document).ready(function() {
    if(window.location.hash) {
        load_song(window.location.hash.substring(1));
    }
});

function load_song(id) {
    $.ajax({
        url: "api/?song_get",
        type: 'POST',
        data: "id=" + id,
        success: function (data) {
            if(data === "err") {
                console.log("An error ocurred.");
                return;
            }

            data = JSON.parse(data);
            $("#song-name").html(data["name"]);
            fnPlaySong(JSON.parse(decodeURIComponent(data["song"])));
        },
    });
}

function play_sample() {
    var sample = [
        ['E,0', 8],
        ['D,0', 8],
        ['C,0', 2],
        ['C,0', 8],
        ['D,0', 8],
        ['C,0', 8],
        ['E,0', 8],
        ['D,0', 1],
        ['C,0', 8],
        ['D,0', 8],
        ['E,0', 2],
        ['A,0', 8],
        ['G,0', 8],
        ['E,0', 8],
        ['C,0', 8],
        ['D,0', 1],
        ['A,0', 8],
        ['B,0', 8],
        ['C,1', 2],
        ['B,0', 8],
        ['C,1', 8],
        ['D,1', 8],
        ['C,1', 8],
        ['A,0', 1],
        ['G,0', 8],
        ['A,0', 8],
        ['B,0', 2],
        ['C,1', 8],
        ['B,0', 8],
        ['A,0', 8],
        ['G,0', 8],
        ['A,0', 1]
    ];

    fnPlaySong(sample);
}

var record_song;
var last_note = null;
var last_hit = null;
window.recording = false;
window.record = record;

function toggle_record() {
    var recordButton = $("#song-record-button");

    if(window.recording) {
        recordButton.removeClass("btn-secondary");
        recordButton.addClass("btn-danger");

        end_record();
    } else {
        recordButton.removeClass("btn-danger");
        recordButton.addClass("btn-secondary");

        init_record();
    }
}
function init_record() {
    console.log("Initialized recording");
    record_song = [];
    window.recording = true;
}
function record(note, octave_modifier) {
    if(!window.recording) return;

    if(last_note && last_hit) {
        var duration = (1 / ((new Date()).getTime() - last_hit)) * 1000;
        var note_duration = [last_note, duration];
        console.log(note_duration);
        record_song.push(note_duration);
    }

    last_note = note + "," + octave_modifier;
    last_hit = (new Date()).getTime();
}
function end_record() {
    if(!window.recording) return;

    record("", "");

    if(record_song.length > 3) {
        var song_title = $("#new-song-title").val();

        $.ajax({
            url: "api/?song_create",
            type: 'POST',
            data: "name=" + song_title + "&song=" + JSON.stringify(record_song),
            success: function (data) {
                if(data === "err") {
                    console.log("An error ocurred.");
                    return;
                }
                location.hash = data;

                console.log("Song saved.");
                fnPlaySong(record_song);
            },
        });
    } else {
        console.log("Your music needs to have at least four notes.");
    }

    window.recording = false;
}