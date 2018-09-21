<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
class bp_core
{

    public $pluginOptions;
    public $pluginSettings;
    public $logErrors;
    public $homeUrl;
    public $useErrorLog = false;
    public $upload_info;
    public $adminNonceString        = "bp_admin_nonce";
    public $siteNonceString         = "bp_site_nonce";
    public $tablePrefix             = "bp_";
    public $keysExepmtedFromSanitize= array();
    public $itemsPerPage;
    public $offset;
    public $USAStates;
    public $siteUrl;
    public $lqMenuCapabilities;
    public $bpOptions            =array();
    public $sliderTextHeading;
    public $sliderTextDescription;
    public $stateLinkOptionName      ="";

    function __construct()
    {

        $this->itemsPerPage                 = 10;
        $this->offset                       = 0;
        $this->pluginOptions                = array('data_type' => '_bp_data_type');
        $this->homeUrl                      = network_home_url();
        $this->siteUrl                      = get_site_url();
        $this->USAStates                    = array(
                                                'AL'=>'ALABAMA',
                                                'AK'=>'ALASKA',
                                                'AS'=>'AMERICAN SAMOA',
                                                'AZ'=>'ARIZONA',
                                                'AR'=>'ARKANSAS',
                                                'CA'=>'CALIFORNIA',
                                                'CO'=>'COLORADO',
                                                'CT'=>'CONNECTICUT',
                                                'DE'=>'DELAWARE',
                                                'DC'=>'DISTRICT OF COLUMBIA',
                                                'FM'=>'FEDERATED STATES OF MICRONESIA',
                                                'FL'=>'FLORIDA',
                                                'GA'=>'GEORGIA',
                                                'GU'=>'GUAM GU',
                                                'HI'=>'HAWAII',
                                                'ID'=>'IDAHO',
                                                'IL'=>'ILLINOIS',
                                                'IN'=>'INDIANA',
                                                'IA'=>'IOWA',
                                                'KS'=>'KANSAS',
                                                'KY'=>'KENTUCKY',
                                                'LA'=>'LOUISIANA',
                                                'ME'=>'MAINE',
                                                'MH'=>'MARSHALL ISLANDS',
                                                'MD'=>'MARYLAND',
                                                'MA'=>'MASSACHUSETTS',
                                                'MI'=>'MICHIGAN',
                                                'MN'=>'MINNESOTA',
                                                'MS'=>'MISSISSIPPI',
                                                'MO'=>'MISSOURI',
                                                'MT'=>'MONTANA',
                                                'NE'=>'NEBRASKA',
                                                'NV'=>'NEVADA',
                                                'NH'=>'NEW HAMPSHIRE',
                                                'NJ'=>'NEW JERSEY',
                                                'NM'=>'NEW MEXICO',
                                                'NY'=>'NEW YORK',
                                                'NC'=>'NORTH CAROLINA',
                                                'ND'=>'NORTH DAKOTA',
                                                'MP'=>'NORTHERN MARIANA ISLANDS',
                                                'OH'=>'OHIO',
                                                'OK'=>'OKLAHOMA',
                                                'OR'=>'OREGON',
                                                'PW'=>'PALAU',
                                                'PA'=>'PENNSYLVANIA',
                                                'PR'=>'PUERTO RICO',
                                                'RI'=>'RHODE ISLAND',
                                                'SC'=>'SOUTH CAROLINA',
                                                'SD'=>'SOUTH DAKOTA',
                                                'TN'=>'TENNESSEE',
                                                'TX'=>'TEXAS',
                                                'UT'=>'UTAH',
                                                'VT'=>'VERMONT',
                                                'VI'=>'VIRGIN ISLANDS',
                                                'VA'=>'VIRGINIA',
                                                'WA'=>'WASHINGTON',
                                                'WV'=>'WEST VIRGINIA',
                                                'WI'=>'WISCONSIN',
                                                'WY'=>'WYOMING',
                                                'AE'=>'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
                                                'AA'=>'ARMED FORCES AMERICA (EXCEPT CANADA)',
                                                'AP'=>'ARMED FORCES PACIFIC'
                                            );

        $this->sliderTextHeading            ="_bp_slider_heading";
        $this->sliderTextDescription        ="_bp_slider_description";
        $this->stateLinkOptionName          ="_bp_state_links";
        $this->bpOptions	                = array(
                                                    $this->sliderTextHeading     		=>"",
                                                    $this->sliderTextDescription 		=>""
                                                );
        $this->lqMenuCapabilities           ="manage_options";
        $this->getPluginSettings();
        add_action('current_screen', array($this, 'check_current_screen'), 99);
    }

    public function setTableNames()
    {
    }

    public function check_current_screen()
    {
        $screen = get_current_screen();
    }

    public function log_error($error, $onlySelected = false)
    {
        if ($this->useErrorLog == true || $onlySelected == true) {
            $this->log(true);
            error_log(print_r($error, true));
        }
    }

    public function log($logError = false)
    {
        $this->errorFileDir = LM_BASE_PATH . '/logs';
        $this->errorFile = $this->errorFileDir . '/error.log';
        if (!file_exists($this->errorFileDir)) {
            @mkdir($this->errorFileDir, 0777, true);
        } else if (substr(fileperms($this->errorFileDir), 0, -3) != '777') {
            @chmod($this->errorFileDir, 0777);
        }

        $this->logErrors = $logError;
        if ($this->logErrors) {
            ini_set('error_log', $this->errorFile);
            if (!file_exists($this->errorFile)) {
                $fh = @fopen($this->errorFile, 'w');
                @fclose($fh);
                @chmod($this->errorFile, 0777);
            }
        }
    }

    function getPluginSettings($param = '')
    {
        $setting = array();

        foreach ($this->pluginOptions as $k => $v) {
            $setting[$k] = get_option($v, NULL);
        }
        $this->pluginSettings = $setting;
        return $setting;
    }

    public function isUrl($string)
    { // check if a url is valid
        $regex = '/^(?:http|https)?(?:\:\/\/)?(?:www.)?(([A-Za-z0-9-]+\.)*[A-Za-z0-9-]+\.[A-Za-z]+)(?:\/.*)?$/im';
        if (preg_match($regex, $string, $matches)) {
            return true;
        }
        return false;
    }

    protected function sanitizeVariables($input)
    {
        $output = array();
        if (is_array($input) && count($input) > 0) {
            foreach ($input as $k => $v) {
                if (!in_array($k, $this->keysExepmtedFromSanitize)) {
                    $output[$k] = sanitize_text_field($v);
                } else {
                    $output[$k] = $v;
                }
            }
        }
        return $output;
    }

    /* Get Data from database */
    /* This function is used to get all data from database */
    public function getAllData($table, $order_by = "", $condition = "", $count = false)
    {
        global $wpdb;
        $where = "";
        if ($condition != "") {
            $where = $condition;
        }
        if ($count == true) {
            $query = " SELECT count(*) as count FROM " . $table . " " . $where . "";
            $myrows = $wpdb->get_var($query);
        } else {
            $query = " SELECT * FROM " . $table . " " . $where . " " . $order_by . "";
            $myrows = $wpdb->get_results($query, ARRAY_A);
        }
        return $myrows;
    }

    /* This function is used to get all data from database according to limit */
    public function getData($table, $condition = "", $count = false)
    {

        global $wpdb;
        $where = "";
        if ($condition != "") {
            $where = $condition;
        }
        if ($count == true) {
            $query = " SELECT count(*) as count FROM " . $table . " " . $where . "";
            $myrows = $wpdb->get_var($query);
        } else {
            $query = " SELECT * FROM " . $table . " " . $where . "";
            $query .= " LIMIT " . $this->offset . "," . $this->itemsPerPage;
            $myrows = $wpdb->get_results($query, ARRAY_A);
        }
        return $myrows;
    }

    /* This function is used to delete record */
    public function deleteRecord($data)
    {
        global $wpdb;
        $tableName = $data['table'];
        $id = $data['id'];

        global $wpdb;
        $delete = $wpdb->query($wpdb->prepare("DELETE FROM " . $tableName . "  WHERE id = %d ", $id));
        if (is_wp_error($delete)) {
            $return['success'] = 0;
            $return['message'] = $wpdb->last_error;

        } else {
            $return['success'] = 1;
            $return['message'] = "Record deleted successfully.";
        }
        return $return;
    }

    /* This function is used to delete record according to key and value */
    public function deleteChildRecord($data)
    {
        global $wpdb;
        $tableName = $data['table'];
        $key = $data['key'];
        $value = $data['value'];

        global $wpdb;
        $delete = $wpdb->query($wpdb->prepare("DELETE FROM " . $tableName . "  WHERE " . $key . " = %d ", $value));
        if (is_wp_error($delete)) {
            $return['success'] = 0;
            $return['message'] = $wpdb->last_error;
        } else {
            $return['success'] = 1;
            $return['message'] = "Record deleted successfully.";
        }
        return $return;
    }

    public function getDbFields($tableName, $labels = false)
    {
        global $wpdb;
        $query = 'SHOW COLUMNS FROM ' . $tableName . '';
        $data = $wpdb->get_results($query);

        if (!$labels) {
            foreach ($data as $k => $v) {
                if (array_key_exists($tableName, $this->specialFields)) {
                    if (array_key_exists($v->Field, $this->specialFields[$tableName])) {
                        $fields[$k] = $this->specialFields[$tableName][$v->Field];
                    } else {
                        $fields[$k] = array(
                            'name' => $v->Field
                        );
                        $fields[$k] = array_merge($fields[$k], $this->getFieldsType($v->Type));
                    }
                } else {
                    $fields[$k] = array(
                        'name' => $v->Field
                    );
                    $fields[$k] = array_merge($fields[$k], $this->getFieldsType($v->Type));
                }
            }
        } else {
            foreach ($data as $k => $v) {
                $fields[$v->Field] = $v->Field;
            }
        }
        return $fields;
    }

    public function getFieldsType($type)
    {
        preg_match('/(.*)\((.*)\)/', $type, $matches);
        if (count($matches) <= 0) {
            $matches[1] = $type;
            $matches[2] = '';
        }
        $typeArray = array(
            'type' => $matches[1],
            'limit' => $matches[2]
        );
        return $typeArray;
    }

    public function createQuery($tableName, $data, $isDate = false)
    {
        global $wpdb;
        $lastId = '';
        $action = $data['action'];
        $currentDate = date("Y-m-d H:i:s");
        $processedData = $this->generateQuery($data, $tableName);
        $where = array(
            'id' => $data['id']
        );
        switch ($action) {
            case 'add':
                if ($isDate) {
                    $processedData['created_date'] = $currentDate;
                    $processedData['modified_date'] = $currentDate;
                }
                $wpdb->insert($tableName, $processedData);
                $lastId = $wpdb->insert_id;
                break;

            case 'edit':
                if ($isDate) {
                    $processedData['modified_date'] = $currentDate;
                }
                $wpdb->update($tableName, $processedData, $where);
                $lastId = $data['id'];
                break;
            default:
        }
        return $lastId;
    }

    public function generateQuery($data, $tableName)
    {
        $fieldsArray = array();

        $fields = $this->getDbFields($tableName);

        foreach ($fields as $k => $v) {
            if (array_key_exists($v['name'], $data)) {
                if ($v['type'] == 'date' || $v['type'] == 'datetime') {
                    $data[$v['name']] = date('Y-m-d', strtotime($data[$v['name']]));
                }
                $fieldsArray = array_merge($fieldsArray, array(
                    $v['name'] => stripcslashes($data[$v['name']])
                ));
            }
        }
        return $fieldsArray;
    }
}