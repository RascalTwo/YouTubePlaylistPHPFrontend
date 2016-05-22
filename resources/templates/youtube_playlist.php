<div id="video_player" class="video_player">
    <iframe frameborder="0" width="640" height="320" src="http://www.youtube.com/embed/PSDLFrVnDas" allowfullscreen></iframe>
    <br>
    <div class="full_page split_column" id="controls">
        <table>
            <tbody>
                <tr>
                    <td>ID:</td>
                    <td><input type="text" id="playlist_id"></td>
                </tr>
                <tr>
                    <td>Add Video:</td>
                    <td><input type="text" id="add_video"></td>
                </tr>
            </tbody>
        </table>
        <p id="controls_response">Response</p>
        <input type="button" value="Load Playlist">

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
            <li>
                <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                Playlist Name
                <p>X videos</p>
                <p>Y length</p>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    document.getElementById("content").className += " flexbox";
    $(document).ready(function(){
        var height = $('body').height() - $('#content').position().top - 25;
        $('.full_page').each(function(){
            $(this).height(height);
        });
    });
</script>