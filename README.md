# Rascal_Two's Homepage

Full of small to medium sized applets.

Powered by PHP.

# TODO

- Youtube Playlist.
    - Private playlists editable via entering password.
- Comics
    - Random comic
    - Latest comic
- Data conversion/visualization.
    - Convert data to other data types as typed.

# Technical TODO

- Finish playlist deletion feature.
- Finish playlist export/import feature.
- Add per-playlist per-video volume values.
- Add per-video play ranges.
    - Start time and End time.
- Disable actual youtube controls.
- Second video column toggleable between public playlists and recommended videos.
- Add video grouping.
    - Could be used to prevent two videos with similar sounds from being played back to back.
- Reduce below-video element margins and/or paddings.
- Have video-column take 50% width instead of exactly 640px width.
- Make video volume a per-playlist setting.
- Show the text-value of the volume slider bar.
- Bind enter buttons to all text fields.
    - Apply video title
    - Create new playlist
    - Change playlist title
- Require password to edit private playlists
    - One password to view playlist
    - Second password to edit playlist
- Change data required to view playlist from name to password.
- Make super complicated API allowing for playlists that automatically generated based on things
    - Channel uploads
        - If title matches regex
    - Youtube searches
- Change from parsing dragged-URL to actually accessing URL and getting data from there.

# Bugs

- Video title needs to have quotes encoded so they'll show up in the text fields in video editing mode.
- Public checkbox during playlist creation doesn't work, all playlists are created public.

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

Re-zero index code:

$this -> video_ids = array_combine(range(0, count($this -> video_ids) - 1), array_values($this -> video_ids));
