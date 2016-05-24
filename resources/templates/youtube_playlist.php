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
                Edit ye playlist
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
                <li>
                    <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                    Video Title
                    <br>
                    Video Details
                </li>
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

    var player;
    var playlist;

    function loadPlaylist(response){
        $("#response_message").html(response.message);
        if (response.status !== 200){
            return;
        }
        $("#video_list").html(response.video_list_html);
        playlist = response.playlist;
        $("#playlist_name").html(response.playlist.name);
        $("#playlist_count").html(response.playlist.video_ids.length);
        $("#playlist_length").html(response.playlist.length);
        if (response.playlist.video_ids.length === 0){
            return;
        }
        loadVideo(response.playlist.video_ids[0]);
    }

    function loadVideo(id){
        console.log("Loading player with id of " + id);
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

    function loadPlaylists(){
        $.get("api/youtube_playlist/playlists", function(response){
            $("#playlist_list").html(response);
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
        loadPlaylists();

        $('html').on('click', '.playlist', function(event){
            $.post("api/youtube_playlist/get_playlist", {by: "id", "id": event.target.id}, function(response){
                loadPlaylist(JSON.parse(response));
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
                    console.log(response);
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
                $.get("api/youtube_playlist/playlists", function(response){
                    $("#playlist_list").html(response);
                });
            });
        });

        $('#load_playlist_button').click(function(){
            var name = $("#load_playlist_name").val();
            $.post("api/youtube_playlist/get_playlist", {by: "name", "name": name}, function(response){
                loadPlaylist(JSON.parse(response));
            });
        })
    });
</script>