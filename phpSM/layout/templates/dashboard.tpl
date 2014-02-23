<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Dashboard - {$app_name}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="{$themepath}/dashboard.css" />
    </head>

    <body>
        <div id="menu">
            <p>Üdv {$username} a VTEPS/VT3PS stream szerveren! <a href="?action=logout">Kijelentkezés</a>
            </p>
        </div>        
        <div id="current_streams">
            <p>
                <caption>Aktuális stream-ek:</caption>
                <ul>
                    {foreach from=$streams item=stream}
                        <li><a href="?action=play_stream&app={$streams_appname}&stream={$stream.name}&st={$stream.secret_token}&e={$stream.expire}">{$stream.name}</a></li>
                        {foreachelse}
                        <li>Most épp senki sem stream-el.</li>
                        {/foreach}
                </ul>
            </p>
        </div>        
        <div id="published_videos">
            <p>
                <caption>Publikált archív felvételek:</caption>
                <ul>
                    {foreach from=$published_vids item=file}
                        <li>
                            <a href="?action=play_stream&app={$published_videos_appname}&stream={$file.name}&st={$file.secret_token}&e={$file.expire}">{$file.name}</a>
                            {if ($can_publish_vid)}
                                <span> -------- <a href="?action=unpublish_recording&recording_name={$file.name}">Publikálás visszavonása</a></span>
                            {/if}
                        </li>
                    {foreachelse}
                        <li>Jelenleg nincsenek publikált felvételek.</li>
                        {/foreach}
                </ul>
            </p>
        </div>
        {if ($can_publish_vid)}
            <div id="recorded_videos">
                <p>
                    <caption>NEM publikált archív felvételek:</caption>
                    <ul>
                        {foreach from=$recorded_vids item=file}
                            <li>
                                <a href="?action=play_stream&app={$recorded_videos_appname}&stream={$file.name}&st={$file.secret_token}&e={$file.expire}">{$file.name}</a>
                                <span> -------- <a href="?action=publish_recording&recording_name={$file.name}">Publikálás</a></span>
                                {if ($can_delete_vid)}
                                    <span> -------- <a href="?action=delete_recording&recording_name={$file.name}">Törlés</a></span>
                                {/if}
                            </li>
                        {/foreach}
                    </ul>
                </p>
            </div>       
        {/if}
        {if ($can_delete_vid)}
            <div id="deleted_videos">
                <p>
                    <caption>Törölt archív felvételek:</caption>
                    <ul>
                        {foreach from=$deleted_vids item=file}
                            <li>
                                {$file.name} <span> -------- <a href="?action=undelete_recording&recording_name={$file.name}">Visszaállítás</a></span>
                            </li>
                        {/foreach}
                    </ul>
                </p>
                <p><a href="?action=show_clankeys">Stream-eléshez segédlet (server/streamkey)</a></p>
            </div>
        {/if}
    </body>
</html>