<?php

/**
 * Description of stream_manager
 *
 * @author Ferenc
 */
class stream_manager {

    // recorder config
    var $config = null;
    // database object
    var $db = null;
    // smarty template object
    var $tpl = null;
    // error messages
    var $error = null;
    // theme path for includes
    var $themepath = null;

    /**
     * class constructor
     */
    function __construct($config = array()) {

        // instantiate the database object
//        $this->db = new db_sqlsrv($config);
        $this->config = $config;
        $this->themepath = 'layout/themes/default';
        // instantiate the template object
        $this->tpl = new streamer_template();
        $this->tpl->assign('themepath', $this->themepath);
    }

    function ni($func) {
        echo('Function ' . $func . ' is not yet implemented<br>');
    }

    function error($message) {
        echo('An error has been occured: <br>' . $message . '<br>');
    }

    function show_message($message) {
        echo $message;
    }

    function show_login() {
        $this->tpl->display("login.tpl");
    }

    function show_dashboard($username, $app, $permissions) {
        $this->tpl->assign('username', $username);

        $streamlist = $this->getCurrentStreamList($app);
        $published_videos = $this->getRecordings('published');
        $recorded_videos = $this->getRecordings('recorded');
        $deleted_videos = $this->getRecordings('deleted');

        $this->tpl->assign('streams_appname', $app);
        $this->tpl->assign('streams', $streamlist);
        $this->tpl->assign('published_videos_appname', 'published');
        $this->tpl->assign('published_vids', $published_videos);
        $this->tpl->assign('recorded_videos_appname', 'recorded');
        $this->tpl->assign('recorded_vids', $recorded_videos);
        $this->tpl->assign('deleted_videos_appname', 'deleted');
        $this->tpl->assign('deleted_vids', $deleted_videos);
        if (($_SESSION['permissions'] & CAN_PUBLISH_RECORDINGS) != 0) {
            $canpublish = true;
        } else
            $canpublish = false;
        $this->tpl->assign('can_publish_vid', $canpublish);
        if (($_SESSION['permissions'] & CAN_DELETE_RECORDINGS) != 0) {
            $candelete = true;
        } else {
            $candelete = false;
        }
        $this->tpl->assign('can_delete_vid', $candelete);
        $canDownload = ($_SESSION['permissions'] & CAN_DOWNLOAD_RECORDINGS) != 0 ? TRUE : FALSE;
        $this->tpl->assign('can_download_vid', $canDownload);
        $this->tpl->display("dashboard.tpl");
    }

    function show_player($app, $stream, $secret_token, $expire) {
        $this->tpl->assign('jwplayer_key', $this->config['jwplayer_key']);
        $this->tpl->assign('streamserver', $this->config['stream_server']);
        $this->tpl->assign('appname', $app);
        $this->tpl->assign('streamname', $stream);
        $this->tpl->assign('st', $secret_token);
        $this->tpl->assign('e', $expire);
        $this->tpl->display("player.tpl");
    }

    function show_clanKeys() {
        foreach ($this->config['clans_allowed'] as $key => $value) {
            $clans[$key] = $this->getClanKeys($key);
        }
        $this->tpl->assign('stream_server', $this->config['stream_server']);
        $this->tpl->assign('clans', $clans);
        $this->tpl->display("clan.tpl");
    }

    function getClanKeys($clan) {
        $clan_members = $this->getClanMembers($clan);
        $clan_list = array();
        $entity = array();
        if ($clan_members !== FALSE) {
            foreach ($clan_members as $member) {
//                $entity['name'] = $member['account_name'];
//                $entity['key'] = $this->getMemberStreamKey('live', $member['account_name'], 'expelliarmus');
                $clan_list[$member['account_name']] = $this->getMemberStreamKey('live', $member['account_name'], $this->config['salt']);
            }
        }
        ksort($clan_list);
        return $clan_list;
    }

    function getMemberStreamKey($app, $name, $salt) {
        $md5 = base64_encode(md5($salt . $app . '/' . $name, true)); // Using binary hashing.
        $md5 = strtr($md5, '+/', '-_'); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
        $md5 = str_replace('=', '', $md5); // When used in query parameters the base64 padding character is considered special.
        return $md5;
    }

    function getClanMembers($clan) {
        foreach ($this->config['clans_allowed'] as $key => $value) {
            if ($key == $clan) {
                $clan_details = json_decode($this->getClanDetailsJSON($value), true);
                return $clan_details["data"][$value]["members"];
            }
        }
        return FALSE;

//        switch ($clan) {
//            case 'VTEPS' :
//                $clan_id = 500002053;
//                $clan_details = json_decode($this->getClanDetailsJSON("500002053"), true);
//                break;
//            case 'VT3PS' :
//                $clan_id = 500025525;
//                $clan_details = json_decode($this->getClanDetailsJSON("500025525"), true);
//                break;
//            default: return FALSE;
//        }
//        return $clan_details["data"][$clan_id]["members"];
    }

    function getClanDetailsJSON($clanid) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'http://api.worldoftanks.eu/2.0/clan/info//?application_id=d0a293dc77667c9328783d489c8cef73&clan_id=' . $clanid);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Clan Checker');
        $results = curl_exec($curl_handle);
        curl_close($curl_handle);

        return $results;
    }

    function getCurrentStreamList($app) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, 'http://localhost/server_stats');
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Clan Checker');
        $results = curl_exec($curl_handle);
        curl_close($curl_handle);
        $parser = simplexml_load_string($results);

        $streams = array();

        foreach ($parser->server->application as $application) {
            if ($application->name == $app) {
                foreach ($application->live->stream as $stream) {
                    if (isset($stream->publishing)) {
                        $expire = time() + $this->config['link_time_valid'];
                        $st = $this->getSecretToken($this->config['salt'], $app, $stream->name, $expire);
                        $stream_name = $stream->name;
                        $stream_entity = array('name' => $stream->name, 'secret_token' => $st, 'expire' => $expire);
                        $streams[] = $stream_entity;
                    }
                }
            }
        }
        return $streams;
    }

    function getRecordings($type) {
        $recordings = array();
        $folder = null;
        switch ($type) {
            case 'published':
                $folder = $this->config['folder_published'];
                $app = 'published';
                break;
            case 'recorded':
                $folder = $this->config['folder_recorded'];
                $app = 'recorded';
                break;
            case 'deleted':
                $folder = $this->config['folder_deleted'];
                $app = 'deleted';
                break;
        }
        if ($folder != null) {
            if ($handle = opendir($folder)) {
                while (false !== ($entry = readdir($handle))) {
                    if (substr($entry, -4) == '.flv') {
                        $name = substr($entry, 0, strlen($entry) - 4);
                        $expire = time() + $this->config['link_time_valid'];
                        $st = $this->getSecretToken($this->config['salt'], $app, $name, $expire);
                        $stp = $this->getSecretToken($this->config['salt'], $app, $name, '0');
                        $recordings[] = array('name' => $name, 'secret_token' => $st, 'secret_token_permalink' => $stp, 'expire' => $expire);
                    }
                }
                closedir($handle);
            }
            asort($recordings);
            return $recordings;
        } else {
            return false;
        }
    }

    function publishRecording($recording_name) {
        $filename = $recording_name . '.flv';
        if (rename($this->config['folder_recorded'] . $filename, $this->config['folder_published'] . $filename)) {
            $this->show_message("Recording $recording_name has been published successfully.");
        } else {
            $this->error("Error publishing $recording_name!!!");
        }
    }

    function unPublishRecording($recording_name) {
        $filename = $recording_name . '.flv';
        if (rename($this->config['folder_published'] . $filename, $this->config['folder_recorded'] . $filename)) {
            $this->show_message("Recording $recording_name has been un-published successfully.");
        } else {
            $this->error("Error un-publishing $recording_name!!!");
        }
    }

    function deleteRecording($recording_name) {
        $filename = $recording_name . '.flv';
        if (rename($this->config['folder_recorded'] . $filename, $this->config['folder_deleted'] . $filename)) {
            $this->show_message("Recording $recording_name has been deleted successfully.");
        } else {
            $this->error("Error deleting $recording_name!!!");
        }
    }

    function unDeleteRecording($recording_name) {
        $filename = $recording_name . '.flv';
        if (rename($this->config['folder_deleted'] . $filename, $this->config['folder_recorded'] . $filename)) {
            $this->show_message("Recording $recording_name has been deleted successfully.");
        } else {
            $this->error("Error deleting $recording_name!!!");
        }
    }

    function getSecretToken($secret, $app, $stream, $expire) {
        $md5 = base64_encode(md5($secret . $app . '/' . $stream . $expire, true)); // Using binary hashing.
        $md5 = strtr($md5, '+/', '-_'); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
        $md5 = str_replace('=', '', $md5); // When used in query parameters the base64 padding character is considered special.
        return $md5;
    }

    public function downloadFLV($type, $flvName) {
        switch ($type) {
            case 'published': $path = $this->config['folder_published'];
                break;
            case 'recorded': $path = $this->config['folder_recorded'];
                break;
            case 'deleted': $path = $this->config['folder_deleted'];
                break;
            default : $path = '';
        }

        $file = $path . $flvName . '.flv';

        if (file_exists($file) && is_readable($file)) {

            $fsize = filesize($file);
            
            header("Content-Disposition: attachment; filename=\"$flvName.flv\"");
            header("Content-Type: application/octet-stream");
            header("Content-Length: " . $fsize);
            ob_flush();
            flush();
            
            set_time_limit(0);
            $fs = @fopen($file, "rb");

            while (!feof($fs)) {
                print(@fread($fs, 1024 * 8));
                ob_flush();
                flush();
            }
            @fclose($fs);
        }
    }

}
