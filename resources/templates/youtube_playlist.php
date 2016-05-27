<span id="content">
    <div id="video_player" class="video_player">
        <script src="https://www.youtube.com/iframe_api"></script>
        <div id="video_wrapper">
        </div>
        <ul id="visible_controls">
            <li id="play_pause_toggle" class="handy"><img src="play_disabled.png" width="32" height="32"></li>
            <li id="previous_video" class="handy"><img class="rotate-180" src="arrow_disabled.png" width="32" height="32"></li>
            <li id="next_video" class="handy"><img src="arrow_disabled.png" width="32" height="32"></li>
            <li id="replay_toggle" class="handy"><img src="replay_false.png" width="32" height="32"></li>
        </ul>
        <br><br><br><br>
        <div class="full_page split_column" id="controls">
            <ul id="visible_controls">
                <li class="select_control handy">New</li>
                <li class="select_control handy">Load</li>
                <li class="select_control handy">Edit</li>
                <li class="select_control handy">Advanced</li>
            </ul>
            <br><br>
            <span class="toggleable_control" id="new-controls">
                <br>
                <table>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td><input type="text" id="new_playlist_name"></td>
                        </tr>
                        <tr>
                            <td>Public</td>
                            <td>
                                <input type="checkbox" id="new_visibility" checked>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" id="new_playlist_button" value="New Playlist">
            </span>
            <span class="toggleable_control" id="load-controls">
                <br>
                <table>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td><input type="text" id="load_playlist_name"></td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" id="load_playlist_button" value="Load Playlist">
            </span>
            <span class="toggleable_control" id="edit-controls">
                <br>
                <table>
                    <tbody>
                        <tr>
                            <td>Shuffle:</td>
                            <td><input type="checkbox" id="shuffle"></td>
                        </tr>
                        <tr>
                            <td><input type="button" id="video_controls_toggle" value="Show Video Controls"></td>
                        </tr>
                        <tr>
                            <td>Playlist Name:</td>
                            <td><input type="text" id="rename_playlist"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="button" value="Rename Playlist" id="rename_playlist_button"></td>
                        </tr>
                        <tr>
                            <td>Public:</td>
                            <td><input type="checkbox" id="current_visibility"></td>
                        </tr>
                    </tbody>
                </table>
            </span>
            <span class="toggleable_control" id="advanced-controls">
                <br>
                <table>
                    <tbody>
                        <tr>
                            <td><input type="button" id="import_button" value="Import"></td>
                            <td><input type="text" id="import_data"></td>
                        </tr>
                        <tr>
                            <td><input type="button" id="import_button" value="Export"></td>
                            <td><input type="text" id="export_data"></td>
                        </tr>
                    </tbody>
                </table>
            </span>
        </div>
        <div class="full_page split_column" id="playlist_info">
            <table>
                <tbody id="playlist_info_table">
                    <tr>
                        <td>Name</td>
                        <td id="playlist_name"></td>
                    </tr>
                    <tr>
                        <td>Video Count</td>
                        <td id="playlist_count"></td>
                    </tr>
                    <tr>
                        <td>Length</td>
                        <td id="playlist_length"></td>
                    </tr>
                </tbody>
            </table>
            <br><br><br>
            <p id="response_message"></p>
        </div>
    </div>
    <div class="full_page" id="sidebar">
        <div class="full_page split_column" id="video_list_wrapper">
            <ul class="full_page" id="video_list">
            </ul>
        </div>
        <div class="full_page split_column" id="playlist_list_wrapper">
            <ul class="full_page" id="playlist_list">
            </ul>
        </div>
    </div>
</span>
<script type="text/javascript">
    document.getElementById("content").className += " flexbox";

    var state = {
        playlist: {
            id: undefined
        },
        player: undefined,
        played: {},
        previous: undefined,
        playing: undefined,
        replay: false,
        controls: {
            video: false,
            playlist: false
        }
    }

    function loadPlaylist(response){
        if (response.status !== 200){
            return false;
        }
        if (state.playlist.id !== response.playlist.id || state.playlist.video_ids.length !== response.playlist.video_ids.length){
            state.played = {};
            for (var i = 0; i < response.playlist.video_ids.length; i++){
                state.played[response.playlist.video_ids[i]] = 0;
            }
        }
        if (state.playlist.id === undefined){
            $("li[id$='_video']").each(function(){
                this.children[0].src = "arrow.png";
            });
        }
        state.playlist = response.playlist;
        $("#video_list").html(state.playlist.video_list_html);
        $("#playlist_name").html(state.playlist.name);
        $("#rename_playlist").val(state.playlist.name);
        $("#shuffle").prop("checked", state.playlist.shuffle);
        $("#current_visibility").prop("checked", !state.playlist.hidden);
        $("#playlist_count").html(state.playlist.video_ids.length);
        $("#playlist_length").html(state.playlist.length);
        $("#playlist_info_table").show();
        if (state.controls.video){
            appendVideoControls();
        }
        return true;
    }

    function loadVideo(id){
        if (!state.replay && state.playing === id){
            loadVideo(getNextVideo());
        }
        $("#play_pause_toggle").children()[0].src = "play_disabled.png";
        state.previous = state.playing;
        state.playing = id;
        var arguments = {
            width: 640,
            height: 320,
            videoId: id,
            events: {
                "onStateChange": playerStateChange,
                "onReady": playerReady
            }
        }
        if (state.player === undefined){
            $("#video_wrapper").replaceWith('<div id="video_wrapper"></div>');
            state.player = new YT.Player('video_wrapper', arguments);
        }
        else{
            state.player.loadVideoById(arguments);
        }
    }

    function getNextVideo(){
        if (state.replay){
            return state.playing;
        }
        var counts = {
            0: [],
            1: []
        };
        var ids = Object.keys(state.played);
        for (var i = 0; i < ids.length; i++){
            counts[state.played[ids[i]]].push(ids[i]);
        }
        if (counts[0].length !== 0){
            if (state.playlist.shuffle){
                console.log("Getting random not-played video");
                return counts[0][Math.floor(Math.random() * counts[0].length)];
            }
            for (var i = 0; i < state.playlist.video_ids.length; i++){
                if (state.played[ids[i]] === 1){
                    continue;
                }
                console.log("Getting next-in-line not-played video");
                return ids[i];
            }
        }
        console.log("All videos have been played.")
        for (var i = 0; i < ids.length; i++){
            state.played[ids[i]] = 0;
        }
        if (state.playlist.shuffle){
            console.log("Getting random not-played video");
            return ids[Math.floor(Math.random() * ids.length)];
        }
        console.log("Getting first video");
        return ids[0];
    }

    function playerStateChange(event){
        if (event.data === 0){
            message("Loading next video...");
            state.played[state.playing] = 1;
            loadVideo(getNextVideo());
        }
        else if (event.data === 1){
            $("#play_pause_toggle").children()[0].src = "pause.png";
        }
        else if (event.data === 2){
            $("#play_pause_toggle").children()[0].src = "play.png";
        }
    }

    function playerReady(event){
        message("Video loaded.");
        state.player.playVideo();
        $("#play_pause_toggle").children()[0].src = "pause.png";
    }

    function loadPlaylistList(){
        $.get("api/youtube_playlist/playlists", function(response){
            $("#playlist_list").html(response);
            if (state.controls.playlist){
                appendPlaylistControls();
            }
        });
    }

    function message(message){
        $("#response_message").html(message);
    }

    function appendVideoControls(){
        var listItems = $('#video_list > li');
        listItems.each(function(index){
            $(this).append('<br>');
            $(this).append('<img class="handy" id="' + this.id + '-remove" src="red_x.png" width="24" height="24">');
            if (index !== 0){
                $(this).append('<img class="rotate-270 handy" id="' + this.id + '-up-reorder" src="arrow.png" width="24" height="24">');
            }
            if (index !== listItems.length - 1){
                $(this).append('<img class="rotate-90 handy" id="' + this.id + '-down-reorder" src="arrow.png" width="24" height="24">');
            }
        });
    }

    function appendPlaylistControls(){
        $('#playlist_list > li').each(function(){
            $(this).append('<br>');
            $(this).append('<img class="handy" id="' + this.id + '-delete" src="red_x.png" width="24" height="24">');
        });
    }

    function adjustHeight(){
        var height = $('html').height() - $('#content').position().top - 25;
        $('.full_page').each(function(){
            $(this).height(height);
        });
    }

    $(document).ready(function(){

        adjustHeight();
        loadPlaylistList();

        $('html').on('click', '.playlist', function(event){
            event.stopPropagation();
            message("Loading playlist...")
            $.post("api/youtube_playlist/get_playlist", {by: "id", "id": event.target.id}, function(response){
                if (!loadPlaylist(JSON.parse(response))){
                    message("Playlist not found.");
                    return;
                }
                message("Playlist loaded.");
                if (state.playlist.video_ids.length === 0){
                    return;
                }
                //loadVideo(playlist.video_ids[0]);
            });
        });

        $('html').on('click', '.video', function(event){
            event.stopPropagation();
            message("Loading video...");
            loadVideo(event.target.id);
        });

        $('html').on('click', 'img[id$="-remove"]', function(event){
            event.stopPropagation();
            message("Removing video...");
            var id = event.target.id.split("-").slice(0, -1).join("-");
            $.post("api/youtube_playlist/remove_video", {playlist: state.playlist.id, id: id}, function(response){
                loadPlaylistList();
                $.post("api/youtube_playlist/get_playlist", {by: "id", "id": state.playlist.id}, function(response){
                    loadPlaylist(JSON.parse(response));
                    message("Video removed.");
                });
            });
        });

        $('html').on('click', 'img[id$="-delete"]', function(event){
            event.stopPropagation();
            message("Deleting playlist...");
            $.post("api/youtube_playlist/delete_playlist", {id: event.target.id.split("-")[0]}, function(response){
                loadPlaylistList();
                message("Playlist deleted.");
            });
        });

        $('html').on('click', 'img[id$="-reorder"]', function(event){
            event.stopPropagation();
            message("Changing video order...");
            var data = event.target.id.split("-");
            var id = data.slice(0, data.length - 2).join("-");
            var dir = data.slice(data.length - 2, data.length - 1).join("");
            $.post("api/youtube_playlist/reorder_video", {playlist: state.playlist.id, id: id, direction: dir}, function(response){
                if (!JSON.parse(response).refresh){
                    message("Video already at end/beginning of playlist.");
                    return;
                }
                loadPlaylistList();
                $.post("api/youtube_playlist/get_playlist", {by: "id", "id": state.playlist.id}, function(response){
                    loadPlaylist(JSON.parse(response));
                    message("Playlist order changed.");
                });
            });
        });

        $("html").on("dragover", function(event){
            event.preventDefault();
            event.stopPropagation();
        });

        $("html").on("dragleave", function(event){
            event.preventDefault();
            event.stopPropagation();
        });

        $('html').on('drop', '#video_list', function(event){
            message("Attempting to add video/playlist...");
            event.preventDefault();
            event.stopPropagation();
            var data = event.originalEvent.dataTransfer.items[0].getAsString(function(text){
                if (state.playlist === undefined){
                    message("Must load a playlist before adding videos.");
                    return;
                }
                var type;
                var id;
                if (text.indexOf("v=") !== -1){
                    type = "v";
                }
                else{
                    type = "list";
                }
                id = text.split(type + "=")[1]
                if (id.indexOf("&") !== -1){
                    id = id.split("&")[0]
                }
                $.post("api/youtube_playlist/add_video", {playlist: state.playlist.id, type: type, id: id}, function(response){
                    loadPlaylistList();
                    $.post("api/youtube_playlist/get_playlist", {by: "id", "id": state.playlist.id}, function(response){
                        loadPlaylist(JSON.parse(response));
                        message("Video(s) added.");
                    });
                });
            });
        });

        $('.select_control').each(function(){
            event.stopPropagation();
            var target = this.innerHTML.toLowerCase();
            $(this).click(function(){
                $(".toggleable_control").each(function(){
                    if (this.id.split("-")[0] === target){
                        if (target === "edit" && state.playlist.id === undefined){
                            message("Can not edit a playlist that has not beed loaded.");
                            return;
                        }
                        $(this).show();
                    }
                    else{
                        $(this).hide();
                    }
                });
            });
        });

        $('#new_playlist_button').click(function(){
            event.stopPropagation();
            var post_data = {
                name: $("#new_playlist_name").val(),
                hidden: !$("#new_visibility").is(":checked")
            };
            if (post_data.name === ""){
                message("Must provide a playlist name.");
                return;
            }
            $.post("api/youtube_playlist/add", post_data, function(response){
                message(response.message);
                loadPlaylistList();
            });
        });

        $('#load_playlist_button').click(function(){
            event.stopPropagation();
            var name = $("#load_playlist_name").val();
            $.post("api/youtube_playlist/get_playlist", {by: "name", "name": name}, function(response){
                if (!loadPlaylist(JSON.parse(response))){
                    message("Playlist not found.");
                    return;
                }
                message("Playlist loaded.");
                if (state.playlist.video_ids.length === 0){
                    return;
                }
                //loadVideo(playlist.video_ids[0]);
            });
        });

        $('#video_controls_toggle').click(function(){
            event.stopPropagation();
            if (state.controls.video){
                state.controls.video = false;
                this.value = "Show Video Controls";
                $("#video_list").html(state.playlist.video_list_html);
                message("Exited Video edit mode.");
                return;
            }
            state.controls.video = true;
            appendVideoControls();
            this.value = "Hide Video Controls";
            message("Entered Video edit mode.");
        })

        $('#playlist_controls_toggle').click(function(){
            event.stopPropagation();
            if (state.controls.playlist){
                state.controls.playlist = false;
                this.value = "Show Playlist Controls";
                loadPlaylistList();
                message("Exited Playlist edit mode.");
                return;
            }
            state.controls.playlist = true;
            appendPlaylistControls();
            this.value = "Hide Playlist Controls";
            message("Entered Playlist edit mode.");
        })

        $('#shuffle').change(function(){
            event.stopPropagation();
            var val = event.target.checked
            message("Setting Shuffle to " + val);
            $.post("api/youtube_playlist/edit_playlist", {playlist: state.playlist.id, shuffle: val}, function(response){
                $.post("api/youtube_playlist/get_playlist", {by: "id", "id": state.playlist.id}, function(response){
                    loadPlaylist(JSON.parse(response));
                    message("Shuffle set to " + val);
                });
            });
        });

        $('#current_visibility').change(function(){
            event.stopPropagation();
            var new_vis = (event.target.checked ? "Public" : "Hidden");
            message("Setting visibility to " + new_vis);
            $.post("api/youtube_playlist/edit_playlist", {playlist: state.playlist.id, hidden: !event.target.checked}, function(response){
                $.post("api/youtube_playlist/get_playlist", {by: "id", "id": state.playlist.id}, function(response){
                    loadPlaylist(JSON.parse(response));
                    message("Visibility set to " + new_vis);
                });
            });
        });

        $("#replay_toggle").click(function(){
            event.stopPropagation();
            state.replay = !state.replay;
            this.children[0].src = "replay_" + state.replay + ".png";
        });

        $("#play_pause_toggle").click(function(){
            event.stopPropagation();
            if (state.player === undefined){
                message("No playlist loaded.");
                return;
            }
            this.children[0].src = (state.player.getPlayerState() === 2 ? "pause" : "play") + ".png"
            if (state.player.getPlayerState() === 2){
                state.player.playVideo();
            }
            else if (state.player.getPlayerState() === 1){
                state.player.pauseVideo();
            }
        });

        $("#previous_video").click(function(){
            event.stopPropagation();
            if (state.previous === undefined){
                message("No previous video found.")
                return;
            }
            loadVideo(state.previous);
        });

        $("#next_video").click(function(){
            event.stopPropagation();
            if (state.playlist.id === undefined){
                message("No playlist loaded.");
                return;
            }
            loadVideo(getNextVideo());
        });

        $("#rename_playlist_button").click(function(){
            event.stopPropagation();
            var newName = $("#rename_playlist").val();
            message("Changing name to '" + newName + "'");
            $.post("api/youtube_playlist/get_playlist", {by: "name", "name": newName}, function(response){
                response = JSON.parse(response);
                if (response.status === 200){
                    message("Invalid playlist name");
                    return;
                }
                $.post("api/youtube_playlist/edit_playlist", {playlist: state.playlist.id, name: newName}, function(response){
                    $.post("api/youtube_playlist/get_playlist", {by: "id", "id": state.playlist.id}, function(response){
                        loadPlaylist(JSON.parse(response));
                        message("Name changed to '" + newName + "'");
                    });
                });
            })
        });

        /* Rename button */
        /* Import button */
        /* Export button */
    });
</script>