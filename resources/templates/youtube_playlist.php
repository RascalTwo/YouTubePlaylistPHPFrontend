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
                            <td>ID</td>
                            <td><input type="text" id="playlist_id"></td>
                        </tr>
                        <tr>
                            <td>Add Video</td>
                            <td><input type="text" id="add_video"></td>
                        </tr>
                    </tbody>
                </table>
                <input type="button" value="Load Playlist">
            </span>
            <span class="toggleable_control" id="edit-controls">
                Edit ye playlist
            </span>
            <p id="control_response">Response</p>
        </div>
        <div class="full_page split_column" id="playlist_info">
            <table>
                <tbody id="playlist_info_table">
                    <tr>
                        <td>Name</td>
                        <td>{{NAME}}</td>
                    </tr>
                    <tr>
                        <td>Video Count</td>
                        <td>{{VIDEO_COUNT}}</td>
                    </tr>
                    <tr>
                        <td>Length</td>
                        <td>{{TOTAL_LENGTH}}</td>
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
                <li>
                    <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                    Video Title
                    <br>
                    Video Details
                </li>
                <li>
                    <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                    Video Title
                    <br>
                    Video Details
                </li>
                <li>
                    <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                    Video Title
                    <br>
                    Video Details
                </li>
                <li>
                    <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                    Video Title
                    <br>
                    Video Details
                </li>
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
    var current_playlist;

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('video_wrapper', {
            width: 640,
            height: 320,
            videoId: 'ApAIQB53KVA',
            events: {
                onReady: initialize
            }
        });
    }

    function initialize(){
        setInterval(function(){
            //console.log(player.getCurrentTime());
            //console.log(player.getDuration());
        }, 5000);
    }

    $(document).ready(function(){
        var height = $('body').height() - $('#content').position().top - 25;
        $('.full_page').each(function(){
            $(this).height(height);
        });

        $.get("api/youtube_playlist/playlists", function(response){
            $("#playlist_list").html(response);
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
                $("#control_response").html("Must provide a playlist name.");
                return;
            }
            $.post("api/youtube_playlist/add", post_data, function(response){
                current_playlist = JSON.parse(response);
                if (current_playlist.hidden){
                    return;
                }
                $.get("api/youtube_playlist/playlists", function(response){
                    $("#playlist_list").html(response);
                });
            });
        });
    });
</script>