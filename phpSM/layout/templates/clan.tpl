<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Clan - {$app_name}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="{$themepath}/dashboard.css" />
    </head>

    <body>
        <div id="clan_players">
            <p>Stream-eléshez az alábbi beállításokat kell használni:</p>
            <p>Server: {$stream_server}</p>
            <p>A stream key felhasználónként változik:</p>
            {foreach from=$clans key=clan_name item=clan}
                <p>{$clan_name}</p>

                <table>
                    <thead>
                        <tr>
                            <th>Név</th>
                            <th>Stream key</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$clan key=name item=streamkey}
                            <tr>
                                <td>{$name}</td>
                                <td>{$name}?key={$streamkey}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {/foreach}
        </div>
    </body>
</html>