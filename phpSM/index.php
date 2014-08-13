<?php

// Define our application directory
define('phpSM_DIR', dirname(__FILE__) . '/');
// Define SMARTY_DIR
define('SMARTY_DIR', phpSM_DIR . 'classes/Smarty/');

// Define permissions
define('CAN_PLAY_STREAMS', 1);
define('CAN_PUBLISH_RECORDINGS', 2);
define('CAN_DELETE_RECORDINGS', 4);
define('CAN_GET_CLANKEYS', 8);
define('CAN_DOWNLOAD_RECORDINGS', 16);
// Loading configuration
require phpSM_DIR . 'config.php';

// Class autoloader function
function class_autoloader($class) {
    if ($class == "Smarty") {
        include phpSM_DIR . 'classes/Smarty/Smarty.class.php';
    } elseif (strpos($class, "Smarty") === false) {
        include phpSM_DIR . 'classes/' . $class . '.class.php';
    }
}

spl_autoload_register('class_autoloader');

session_start();

$sm = new stream_manager($CONFIG);

$_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'dashboard';

if (isset($_SESSION["username"]) && ($_SESSION["username"] != '')) {
    if ($_SESSION["valid"]) {

        if (!isset($_SESSION["permissions"])) {
            switch ($_SESSION["role"]) {
                case 'private':
                    $_SESSION["permissions"] = 1;
                    break;
                case 'leader':
                    $_SESSION["permissions"] = 31;
                    break;
                case 'vice_leader':
                    $_SESSION["permissions"] = 1;
                    break;
                case 'commander':
                    $_SESSION["permissions"] = 1;
                    break;
                case 'recruit':
                    $_SESSION["permissions"] = 1;
                    break;
                case 'diplomat':
                    $_SESSION["permissions"] = 1;
                    break;
                case 'recruiter':
                    $_SESSION["permissions"] = 1;
                    break;
                case 'treasurer':
                    $_SESSION["permissions"] = 17;
                    break;
            }
            if ($_SESSION['username'] == 'FuFe') {
                $_SESSION["permissions"] = 31;
            }
        }

        switch ($_action) {
            case 'guestplayer':
                $sm->show_player($_REQUEST['app'], $_REQUEST['stream'], $_REQUEST['st'], 0);
                break;
            case 'play_stream':
                if (($_SESSION['permissions'] & CAN_PLAY_STREAMS) != 0) {
                    $sm->show_player($_REQUEST['app'], $_REQUEST['stream'], $_REQUEST['st'], $_REQUEST['e']);
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'show_clankeys':
                if (($_SESSION['permissions'] & CAN_GET_CLANKEYS) != 0) {
                    $sm->show_clanKeys();
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'logout':
                session_destroy();
                $sm->show_message("Logged out successfully.");
                break;
            case 'publish_recording':
                if (($_SESSION['permissions'] & CAN_PUBLISH_RECORDINGS) != 0) {
                    $sm->publishRecording($_REQUEST['recording_name']);
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'unpublish_recording':
                if (($_SESSION['permissions'] & CAN_PUBLISH_RECORDINGS) != 0) {
                    $sm->unPublishRecording($_REQUEST['recording_name']);
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'delete_recording':
                if (($_SESSION['permissions'] & CAN_DELETE_RECORDINGS) != 0) {
                    $sm->deleteRecording($_REQUEST['recording_name']);
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'undelete_recording':
                if (($_SESSION['permissions'] & CAN_DELETE_RECORDINGS) != 0) {
                    $sm->unDeleteRecording($_REQUEST['recording_name']);
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'download':
                if (($_SESSION['permissions'] & CAN_DOWNLOAD_RECORDINGS) != 0) {
                    $sm->downloadFLV($_REQUEST['type'], $_REQUEST['recording']);
                } else {
                    $sm->error("ACCESS DENIED!");
                }
                break;
            case 'dashboard':
            default:
                $sm->show_dashboard($_SESSION['username'], 'live', $_SESSION['permissions']);
                break;
        }
    } else {
        echo("Hello " . $_SESSION["username"] . "! Sajnos csak VTEPS es VT3PS tagok lephetnek be!");
    }
} else {
    switch ($_action) {
        case 'guestplayer':
            $sm->show_player($_REQUEST['app'], $_REQUEST['stream'], $_REQUEST['st'], 0);
            break;
        default:
            $sm->show_login();
            break;
    }
}
