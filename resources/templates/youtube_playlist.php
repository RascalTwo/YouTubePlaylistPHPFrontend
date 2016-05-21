<div id="video_player" class="video_player flexbox-col">
    <iframe frameborder="0" width="640" height="320" src="http://www.youtube.com/embed/PSDLFrVnDas" allowfullscreen></iframe>
</div>
<div id="sidebar">
    <div id="video_list_wrapper">
        Playlist ""
        <br>
        Videos in "": X
        <br>
        Full length of "": Y
        <ul id="video_list" class="opened right flexbox-col">
            <li>
                <p>Video Title</p>
                <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
            </li>
            <li>
                <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
                super duper long title of awesomeness doot doot
            </li>
            <li>
                <p>Video Title</p>
                <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
            </li>
            <li>
                <p>Video Title</p>
                <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
            </li>
            <li>
                <p>Video Title</p>
                <img src="https://i.ytimg.com/vi/ApAIQB53KVA/hqdefault.jpg" width="120" height="90">
            </li>
        </ul>
    </div>
    <div id="controls">
        ID: <input type="text" id="playlist_id">
        <br>
        Add Video: <input type="text" id="add_video">
        <br>
        <p id="playlist_load_response">Response</p>
        <input type="button" value="Load Playlist">
        <ul id="playlist_list">
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
</script>