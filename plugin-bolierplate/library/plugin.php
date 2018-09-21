<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
class bp_plugin extends bp_shortcode
{

    public $logError = false;

    public static function init()
    {
        new self;
    }

    public function __construct()
    {
        parent::__construct();

        add_action('admin_menu', array($this,'createAdminMenu'));
        add_action('admin_enqueue_scripts', array($this,'addScriptsAndStyles'));
        add_action('wp_enqueue_scripts', array($this,'addScriptsAndStylesOnFront'));

        add_action('wp_ajax_bp_ajax', array($this,'bp_admin_ajax'));
        add_action('wp_ajax_nopriv_bp_ajax', array($this,'bp_admin_ajax'));
        add_filter("body_class", array($this,"add_body_class"));
        add_action('admin_init', array($this,'bp_admin_init'));
        add_action('admin_notices', array($this,'bp_admin_notices'));
        add_action( 'admin_init', array($this,'bp_register_settings'));

        //add_action('wp_footer', array($this,'includeFooterScripts'), 20);

        register_activation_hook(bp_FILE, array($this,'bp_activate'));

        add_action('wp_ajax_get_security_token', array($this,'get_security_token'));
        add_action('wp_ajax_nopriv_get_security_token', array($this,'get_security_token'));
    }


    function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return;
        }
        $type = $this->FriendlyErrorType($errno);
        $this->errorMessage .= "Error: [" . $type . "] $errstr in $errfile on line number $errline\n";

        /* Don't execute PHP internal error handler */
        return true;
    }

    public function handleErrors()
    {
        $error = error_get_last();

        # Checking if last error is a fatal error
        if (($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR)) {
            # Here we handle the error, displaying HTML, logging, ...
            $type = $this->FriendlyErrorType($error['type']);
            $this->errorMessage .= "Error: [" . $type . "] " . $error['message'] . " in " . $error['file'] . " on line number " . $error['line'];
            $result["success"] = false;
            $result["message"] = $this->errorMessage;
            header('content-type: application/json');
            $response = $result;
            echo json_encode($response);
            die();
        } else if ($error['type'] != "") {
            $type = $this->FriendlyErrorType($error['type']);
            $this->errorMessage .= "Error: [" . $type . "] " . $error['message'] . " in " . $error['file'] . " on line number " . $error['line'];
        }
    }

    public function FriendlyErrorType($type)
    {
        switch ($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }
        return "";
    }

    public function bp_activate()
    {

        $notices = get_option('_bp_admin_notices', array());
        $indexedPosts = get_option('_bp_indexed_posts');
        if ($indexedPosts != "" && $indexedPosts != 0 && $indexedPosts != false) {
            $syncStatus = parent::getSyncStatus();
            if ($syncStatus) {
                $msg = bp_plugin::admin_reindex_messages();
                if (!$this->checkNotices($notices, "recommended to")) {
                    $notices[] = $msg;
                }
            }

        } else {
            $msg = bp_plugin::admin_notice_messages();
            if (!$this->checkNotices($notices, "been activated")) {
                $notices[] = $msg;
            }
        }
        update_option('_bp_admin_notices', $notices);
    }

    public function checkNotices($notices, $word)
    {
        if (count($notices) > 0) {
            foreach ($notices as $k => $v) {
                if (strpos($v, $word) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function admin_notice_messages()
    {
        return "";
    }

    public function bp_admin_init()
    {
        global $bpAPIClient;
        $current_version = LM_PLUGIN_VERSION;

    }

    function bp_admin_notices()
    {
        $notices = get_option('_bp_admin_notices', array());

        if (count($notices) > 0) {
            foreach ($notices as $notice) {
                echo "<div class='update-nag bp-notices'>$notice</div>";
            }
            delete_option('_bp_admin_notices');
        }
    }


    public function add_body_class($classes)
    {
        global $post;
        $classes[] = "bp_search_page";
        return $classes;
    }

    public function getDomain()
    {
        $domain         = get_option('siteurl');
        $find           = array('http://','https://');
        $replace        = array('','');
        $domain         = str_replace($find, $replace, $domain);
        $this->domain   = strtolower($domain);
        return $this->domain;
    }

    public function createAdminMenu()
    {
        add_menu_page(__('Bp Settings', 'bp'), __('Bp Settings', 'bp'), 'manage_options', 'bp', array($this,"manageSettings"));
        //add_submenu_page('bp',__('Manage States', 'bp'), __('Manage States', 'bp'),$this->lqMenuCapabilities, 'state', array($this, "manageState"));
        //add_submenu_page('bp',__('Settings', 'bp'), __('Settings', 'bp'),$this->lqMenuCapabilities, 'settings', array($this, "manageSettings"));
    }

    public function addScriptsAndStyles($hook)
    {
        wp_register_style('bp_admin_css', LM_BASE_URL . '/assets/css/backend/stylesheet.css', false, time());
        wp_enqueue_style('bp_admin_css');
        wp_register_script('bp_admin_validate_js', LM_BASE_URL . '/assets/js/backend/jquery.validate.min.js', array('jquery'), time());
        wp_register_script('bp_admin_js', LM_BASE_URL . '/assets/js/backend/script.js', array('jquery'), time());

        wp_enqueue_script('bp_admin_validate_js');
        wp_enqueue_script('bp_admin_js');
    }

    public function includeFooterScripts()
    {
        ?>
        <script type="text/javascript">
            var adminAjax           = '<?php echo admin_url('admin-ajax.php');?>';
            var bps_site_nonce   = "<?php echo wp_create_nonce($this->siteNonceString);?>";

        </script>
    <?php }

    public function addScriptsAndStylesOnFront()
    {
        if (!is_admin()) {
            wp_register_style('bp_front_css', LM_BASE_URL . '/assets/css/frontend/stylesheet.css', false, time());
            wp_enqueue_style('bp_front_css');
            if (!wp_script_is('jquery', 'enqueued')) {
                wp_enqueue_script('jquery');
            }
        }
    }

    public function manageState()
    {
       include_once(LM_BASE_PATH.'/templates/backend/states.php');

    }
    public function manageSettings()
    {
        include_once(LM_BASE_PATH.'/templates/backend/settings.php');
    }

    public function returnOnDie($message)
    {
        if ($this->resultSent == 0) {
            $result["success"]  = false;
            $result["message"]  = $message;
            $result["errors"]   = $this->errorMessage;
            header('content-type: application/json');
            $response           = $result;
            echo json_encode($response);
            exit;
        }
    }

    public function dieHandler($param)
    {
        die();
    }

    public function checkIfInAdmin()
    {
        if ((strpos(strtolower($_SERVER[HTTP_REFERER]), strtolower(site_url())) !== FALSE && strpos(strtolower($_SERVER[HTTP_REFERER]), "wp-admin") !== FALSE)) {
            return true;
        }
        return false;
    }

    private function checkSecurity($nonce)
    {
        if (!$this->checkIfInAdmin()) {
            register_shutdown_function(array($this,'returnOnDie'), 'Invalid nonce on frontend.');
            if (check_ajax_referer($this->siteNonceString, 'security')) {
                return true;
            }
        } else if (current_user_can("activate_plugins")) {
            register_shutdown_function(array($this,'returnOnDie'), 'Invalid nonce on admin.');
            if (check_ajax_referer($this->adminNonceString, 'security')) {
                return true;
            }
        }
        return false;
    }

    public function bp_admin_ajax()
    {
        error_reporting(0);
        register_shutdown_function(array($this,'handleErrors'));
        set_error_handler(array($this,"errorHandler"));
        if (!empty($_POST)) {
            $task = $_POST['task'];
            $nonce = $_POST['security'];
            add_filter('wp_die_ajax_handler', array($this,'dieHandler'));
            if ($this->checkSecurity($nonce)) {
                $post = $this->sanitizeVariables($_POST);
                switch ($task) {
                    case "get_states":
                        $result = $this->saveState($post);
                        break;
                    default:
                        $result = array('success' => 0, 'message' => 'Parameter missing, please try again.');
                }
            } else {
                $result = array('success' => 0, 'message' => 'Insecure request, please try again.');
            }
        } else {
            $result = array('success' => 0, 'message' => 'Invalid request, please try again.');
        }

        header('content-type: application/json');
        $result['errors'] = $this->errorMessage;
        $response = $result;
        $this->resultSent = 1;
        echo json_encode($response);
        exit;
    }
    /* This function is used to register plugin options */
    Public function bp_register_settings() {
        if(count($this->bpOptions) > 0){
            foreach($this->bpOptions as $slug => $value){
                add_option( $slug, $value);
                register_setting( 'bp_options_group', $slug, 'bp_callback' );
            }
        }
    }
}
