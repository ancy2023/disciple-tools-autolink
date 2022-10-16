<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly.


/**
 * Class Disciple_Tools_Autolink_Magic_User_App
 */
class Disciple_Tools_Autolink_Magic_User_App extends DT_Magic_Url_Base
{
    public $page_title = 'Autolink';
    public $page_description = 'Autolink user app';
    public $root = "autolink";
    public $type = 'app';
    public $post_type = 'user';
    private $meta_key = 'autolink-app';
    public $show_bulk_send = false;
    public $show_app_tile = false;
    public $functions;

    private static $_instance = null;
    public $meta = []; // Allows for instance specific data.

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct()
    {
        /**
         * Specify metadata structure, specific to the processing of current
         * magic link type.
         *
         * - meta:              Magic link plugin related data.
         *      - app_type:     Flag indicating type to be processed by magic link plugin.
         *      - post_type     Magic link type post type.
         *      - contacts_only:    Boolean flag indicating how magic link type user assignments are to be handled within magic link plugin.
         *                          If True, lookup field to be provided within plugin for contacts only searching.
         *                          If false, Dropdown option to be provided for user, team or group selection.
         *      - fields:       List of fields to be displayed within magic link frontend form.
         */
        $this->meta = [
            'app_type'      => 'magic_link',
            'post_type'     => $this->post_type,
            'contacts_only' => false,
            'fields'        => [
                [
                    'id'    => 'name',
                    'label' => 'Name'
                ]
            ]
        ];

        $this->meta_key = $this->root . '_' . $this->type . '_magic_key';

        parent::__construct();

        $this->functions = Disciple_Tools_Autolink_Magic_Functions::instance();


        /**
         * user_app and module section
         */
        add_filter('dt_settings_apps_list', [$this, 'dt_settings_apps_list'], 10, 1);
        add_action('rest_api_init', [$this, 'add_endpoints']);

        /**
         * tests if other URL
         */
        $url = dt_get_url_path();

        if (strpos($url, $this->root . '/' . $this->type) === false) {
            return;
        }

        /**
         * tests magic link parts are registered and have valid elements
         */
        if (!$this->check_parts_match()) {
            return;
        }



        // load if valid url
        wp_set_current_user($this->parts['post_id']);
        add_action('dt_blank_body', function () {
            $this->ready();
            $this->routes();
        });
        add_filter('dt_magic_url_base_allowed_css', [$this->functions, 'dt_magic_url_base_allowed_css'], 10, 1);
        add_filter('dt_magic_url_base_allowed_js', [$this->functions, 'dt_magic_url_base_allowed_js'], 10, 1);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts'], 100);
    }

    public function ready()
    {
        wp_set_current_user($this->parts['post_id']);
        $this->functions->add_session_leader();
    }

    public function wp_enqueue_scripts()
    {
        $this->functions->wp_enqueue_scripts();
        wp_localize_script(
            'magic_link_scripts',
            'magic',
            [
                'parts' => $this->parts,
                'rest_namespace' => $this->root . '/v1/' . $this->type,
            ]
        );
    }

    /**
     * Builds magic link type settings payload:
     * - key:               Unique magic link type key; which is usually composed of root, type and _magic_key suffix.
     * - url_base:          URL path information to map with parent magic link type.
     * - label:             Magic link type name.
     * - description:       Magic link type description.
     * - settings_display:  Boolean flag which determines if magic link type is to be listed within frontend user profile settings.
     *
     * @param $apps_list
     *
     * @return mixed
     */
    public function dt_settings_apps_list($apps_list)
    {
        $apps_list[$this->meta_key] = [
            'key'              => $this->meta_key,
            'url_base'         => $this->root . '/' . $this->type,
            'label'            => $this->page_title,
            'description'      => $this->page_description,
            'settings_display' => true
        ];

        return $apps_list;
    }

    public function routes()
    {
        $action = $_GET['action'] ?? '';

        $type = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($type === 'GET') {
            switch ($action) {
                case 'survey':
                    $this->show_survey();
                    break;
                default:
                    $this->show_app();
                    break;
            }
            return;
        }

        if ($type === 'POST') {
            switch ($action) {
                default:
                    wp_redirect('/' . $this->root);
            }
            return;
        }
    }

    public function show_app()
    {
        $logo_url = $this->functions->fetch_logo();
        $greeting = __('Hello,', 'disciple-tools-autolink');
        $user_name = dt_get_user_display_name(get_current_user_id());
        $coached_by_label = __('Coached by', 'disciple-tools-autolink');
        $link_heading = __('My Link', 'disciple-tools-autolink');
        $share_link_help_text = __('Copy this link and share it with your coach.', 'disciple-tools-autolink');
        $churches_heading = __('My Churches', 'disciple-tools-autolink');
        $share_link = $this->functions->get_share_link();
        $churches = [];
        $group_fields = DT_Posts::get_post_field_settings('groups');
        $church_fields = [
            'health_metrics' => $group_fields['health_metrics']['default'] ?? [],
        ];
        $church_health_field = $church_fields['health_metrics'];
        $allowed_church_count_fields = [
            'member_count',
            'leader_count',
            'believer_count',
            'baptized_count',
            'baptized_in_group_count'
        ];
        $church_count_fields = [];
        foreach ($allowed_church_count_fields as $field) {
            $church_count_fields[$field] = $group_fields[$field];
        }

        $contact =  Disciple_Tools_Users::get_contact_for_user(get_current_user_id());
        $coach = null;
        $coach_name = '';

        if ($contact) {
            $contact = DT_Posts::get_post('contacts', $contact);
            $churches = DT_Posts::list_posts('groups', [
                'assigned_to' => [get_current_user_id()]
            ], false)['posts'] ?? [];
        }
        if ($contact && count($contact['coached_by'])) {
            $coach = $contact['coached_by'][0] ?? null;
            if ($coach) {
                $coach = DT_Posts::get_post('contacts', $coach['ID']);
                $coach_name = $coach['name'] ?? '';
            }
        }

        include('templates/app.php');
    }

    public function show_survey()
    {
        include('templates/survey.php');
    }

    /**
     * Register REST Endpoints
     * @link https://github.com/DiscipleTools/disciple-tools-theme/wiki/Site-to-Site-Link for outside of wordpress authentication
     */
    public function add_endpoints()
    {
        $namespace = $this->root . '/v1';
        register_rest_route(
            $namespace,
            '/' . $this->type,
            [
                [
                    'methods'  => "GET",
                    'callback' => [$this, 'endpoint_get'],
                    'permission_callback' => function (WP_REST_Request $request) {
                        $magic = new DT_Magic_URL($this->root);

                        return $magic->verify_rest_endpoint_permissions_on_post($request);
                    },
                ],
            ]
        );
        register_rest_route(
            $namespace,
            '/' . $this->type,
            [
                [
                    'methods'  => "POST",
                    'callback' => [$this, 'update_record'],
                    'permission_callback' => function (WP_REST_Request $request) {
                        $magic = new DT_Magic_URL($this->root);

                        return $magic->verify_rest_endpoint_permissions_on_post($request);
                    },
                ],
            ]
        );
    }

    public function update_record(WP_REST_Request $request)
    {
        $params = $request->get_params();
        $params = dt_recursive_sanitize_array($params);
        $post_id = $params["parts"]["post_id"]; //has been verified in verify_rest_endpoint_permissions_on_post()


        $args = [];
        if (!is_user_logged_in()) {
            $args["comment_author"] = "Magic Link Submission";
            wp_set_current_user(0);
            $current_user = wp_get_current_user();
            $current_user->add_cap("magic_link");
            $current_user->display_name = "Magic Link Submission";
        }

        if (isset($params["update"]["comment"]) && !empty($params["update"]["comment"])) {
            $update = DT_Posts::add_post_comment($this->post_type, $post_id, $params["update"]["comment"], "comment", $args, false);
            if (is_wp_error($update)) {
                return $update;
            }
        }

        if (isset($params["update"]["start_date"]) && !empty($params["update"]["start_date"])) {
            $update = DT_Posts::update_post($this->post_type, $post_id, ["start_date" => $params["update"]["start_date"]], false, false);
            if (is_wp_error($update)) {
                return $update;
            }
        }

        return true;
    }

    public function endpoint_get(WP_REST_Request $request)
    {
        $params = $request->get_params();
        if (!isset($params['parts'], $params['action'])) {
            return new WP_Error(__METHOD__, "Missing parameters", ['status' => 400]);
        }

        $data = [];

        $data[] = ['name' => 'List item']; // @todo remove example
        $data[] = ['name' => 'List item']; // @todo remove example

        return $data;
    }
}
Disciple_Tools_Autolink_Magic_User_App::instance();
