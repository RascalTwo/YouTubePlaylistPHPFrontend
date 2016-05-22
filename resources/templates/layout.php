<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $page_title ?></title>

        <link rel="stylesheet" type="text/css" href="header.css">
        <?php if ($spreadsheet !== ""){ ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $spreadsheet; ?>.css">
        <?php } ?>
        <script type="text/javascript" src="jquery-2.2.3.min.js"></script>
    </head>
    <body>
        <span id="navigation">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="/youtube_playlist">Youtube Playlists</a></li>
                <li><a href="/comics">Comics</a></li>
            </ul>
        </span>
        <br><br>
        <span id="content">
            <?php include $template_path; ?>
        </span>
    </body>
</html>
