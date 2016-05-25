<span id="content">
    <div id="video_player" class="video_player">
        <script src="https://www.youtube.com/iframe_api"></script>
        <div id="video_wrapper">

        </div>
        <br>
        <div class="full_page split_column" id="controls">
            <ul id="visible_controls">
                <li class="select_control handy">New</li>
                <li class="select_control handy">Load</li>
                <li class="select_control handy">Edit</li>
            </ul>
            <br><br>
            <span class="toggleable_control" id="new-controls">
                <table>
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td><input type="text" id="new_playlist_name"></td>
                        </tr>
                        <tr>
                            <td>Visibility</td>
                            <td>
                                <input type="radio" id="hidden-visibility" name="visibility" value="true">
                                <label for="hidden-visibility">Hidden</label>
                                <br>
                                <input type="radio" id="public-visibility" name="visibility" value="false" checked>
                                <label for="public-visibility">Public</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" id="new_playlist_button" value="New Playlist">
            </span>
            <span class="toggleable_control" id="load-controls">
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
                <input type="button" id="video_controls_toggle" value="Show Video Controls">
                <br>
            </span>
            <p id="response_message">Response</p>
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
        playlist : {
            plays: {

            }
        },
        controls: false
    }

    var player;
    var playlist;

    function loadPlaylist(response){
        $("#response_message").html(response.message);
        if (response.status !== 200){
            return false;
        }
        playlist = response.playlist;
        $("#video_list").html(playlist.video_list_html);
        $("#playlist_name").html(playlist.name);
        $("#playlist_count").html(playlist.video_ids.length);
        $("#playlist_length").html(playlist.length);
        if (state.controls){
            appendControls();
        }
        return true;
    }

    function loadVideo(id){
        console.log("Loading player with id of " + id);
        $("#video_wrapper").replaceWith('<div id="video_wrapper"></div>');
        player = new YT.Player('video_wrapper', {
            width: 640,
            height: 320,
            videoId: id
        });
    }

    function onYouTubeIframeAPIReady(){
        console.log("YT API Ready");
        loadVideo("8tPnX7OPo0Q");
    }

    function loadPlaylistList(){
        $.get("api/youtube_playlist/playlists", function(response){
            $("#playlist_list").html(response);
        });
    }

    function appendControls(){
        $('#video_list > li').each(function(){
            $(this).append('<br>');
            $(this).append('<img class="handy" id="' + this.id + '-remove" src="red_x.png" width="24" height="24">');
            $(this).append('<img class="handy" id="' + this.id + '-up-reorder" src="up_arrow.png" width="24" height="24">');
            $(this).append('<img class="handy" id="' + this.id + '-down-reorder" src="down_arrow.png" width="24" height="24">');
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
            $.post("api/youtube_playlist/get_playlist", {by: "id", "id": event.target.id}, function(response){
                if (!loadPlaylist(JSON.parse(response))){
                    return;
                }
                if (playlist.video_ids.length === 0){
                    return;
                }
                //loadVideo(playlist.video_ids[0]);
            });
        });

        $('html').on('click', '.video', function(event){
            loadVideo(event.target.id);
        });

        $('html').on('click', 'img[id$="-remove"]', function(event){
            event.stopPropagation();
            $.post("api/youtube_playlist/remove_video", {playlist: playlist.id, id: event.target.id.split("-")[0]}, function(response){
                loadPlaylistList();
                $.post("api/youtube_playlist/get_playlist", {by: "id", "id": playlist.id}, function(response){
                    loadPlaylist(JSON.parse(response));
                });
            });
        });

        $('html').on('click', 'img[id$="-reorder"]', function(event){
            event.stopPropagation();
            var data = event.target.id.split("-")
            $.post("api/youtube_playlist/reorder_video", {playlist: playlist.id, id: data[0], direction: data[1]}, function(response){
                if (!JSON.parse(response).refresh){
                    return;
                }
                loadPlaylistList();
                $.post("api/youtube_playlist/get_playlist", {by: "id", "id": playlist.id}, function(response){
                    loadPlaylist(JSON.parse(response));
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
            event.preventDefault();
            event.stopPropagation();
            var data = event.originalEvent.dataTransfer.items[0].getAsString(function(text){
                if (playlist === undefined){
                    $("#response_message").html("Must load a playlist before adding videos.");
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
                $.post("api/youtube_playlist/add_video", {playlist: playlist.id, type: type, id: id}, function(response){
                    loadPlaylistList();
                    $.post("api/youtube_playlist/get_playlist", {by: "id", "id": playlist.id}, function(response){
                        loadPlaylist(JSON.parse(response));
                    });
                });
            });
        });

        $('.select_control').each(function(){
            var target = this.innerHTML.toLowerCase();
            $(this).click(function(){
                $(".toggleable_control").each(function(){
                    if (this.id.split("-")[0] === target){
                        $(this).show();
                    }
                    else{
                        $(this).hide();
                    }
                });
            });
        });

        $('#new_playlist_button').click(function(){
            var post_data = {
                name: $("#new_playlist_name").val(),
                hidden: $("input[name=visibility]:checked").val()
            };
            if (post_data.name === ""){
                $("#response_message").html("Must provide a playlist name.");
                return;
            }
            $.post("api/youtube_playlist/add", post_data, function(response){
                $("#response_message").html(response.message);
                loadPlaylistList();
            });
        });

        $('#load_playlist_button').click(function(){
            var name = $("#load_playlist_name").val();
            $.post("api/youtube_playlist/get_playlist", {by: "name", "name": name}, function(response){
                if (!loadPlaylist(JSON.parse(response))){
                    return;
                }
                if (playlist.video_ids.length === 0){
                    return;
                }
                //loadVideo(playlist.video_ids[0]);
            });
        });

        $('#video_controls_toggle').click(function(){
            if (state.controls){
                state.controls = false;
                this.value = "Show Video Controls";
                $("#video_list").html(playlist.video_list_html);
                return;
            }
            state.controls = true;
            appendControls();
            this.value = "Hide Video Controls";
        })

        $('html').on('change', 'input[name=shuffle]', function(){

        });
    });
</script>