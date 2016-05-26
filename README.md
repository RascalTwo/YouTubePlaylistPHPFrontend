# Rascal_Two's Homepage

Full of small to medium sized applets.

Powered by PHP.

# TODO

- Youtube Playlist.
    - Save playlists.
    - Have public and private playlists.
        - Private playlists loadable via entering the playlist name.
        - Private playlists editable via entering password.
    - Playlists contain videos, playable on repeat/shuffle.
    - Playlist videos can be re-ordered.
- Comics
    - Random comic
    - Latest comic

# Technical TODO

Finish playlist deletion feature.
Finish playlist export/import feature.
Add per-video volume values.

Replace checkboxes with sliders:

<label class="switch">
    <input type="checkbox" id="shuffle">
    <div class="slider round"></div>
</label>

.switch{
    position: relative;
    display: inline-block;
    width: 64px;
    height: 32px;
}

.switch input{
    display: none;
}

.slider{
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: -2px;
    bottom: 0;
    background-color: #F39621;
    transition: .5s;
}

.slider:before{
    position: absolute;
    content: "";
    height: 25px;
    width: 25px;
    left: 4px;
    bottom: 2px;
    background-color: white;
    transition: .5s;
}

input:checked + .slider{
    background-color: #2196F3;
}

input:focus + .slider{
    box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before{
    transform: translateX(26px);
}

.slider.round{
    border-radius: 34px;
}

.slider.round:before{
    border-radius: 50%;
}
