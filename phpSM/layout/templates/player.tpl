<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Stream Player - {$app_name}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="{$themepath}/player.css" />
        <script type="text/javascript" src="jwplayer/jwplayer.js"></script>
        <script type="text/javascript">jwplayer.key = "{$jwplayer_key}";</script>        
    </head>

    <body>
        <div class="spiffyfg">
            <p id="information"><center><img src="layout/images/banner.png" /></center></p>
            <center>
                <div id="myElement">Loading the player...</div>
            </center>
        </div>

        <script type="text/javascript">
            jwplayer("myElement").setup({
                file: "{$streamserver}/{$appname}/{$streamname}?st={$st}&e={$e}",
                rtmp: {
                    bufferlength: 0.1
                },
                width: 700,
                height: 400,
                autostart: true
            });
        </script>

    </body>

</html>