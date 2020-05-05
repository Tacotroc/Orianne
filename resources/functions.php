<?php

/**
 * Do not edit anything in this file unless you know what you're doing
 */

use Roots\Sage\Config;
use Roots\Sage\Container;
use \Mailjet\Resources;

/**
 * Helper function for prettying up errors
 * @param string $message
 * @param string $subtitle
 * @param string $title
 */
$sage_error = function ($message, $subtitle = '', $title = '') {
    $title = $title ?: __('Sage &rsaquo; Error', 'sage');
    $footer = '<a href="https://roots.io/sage/docs/">roots.io/sage/docs/</a>';
    $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
    wp_die($message, $title);
};

/**
 * Ensure compatible version of PHP is used
 */
if (version_compare('7.1', phpversion(), '>=')) {
    $sage_error(__('You must be using PHP 7.1 or greater.', 'sage'), __('Invalid PHP version', 'sage'));
}

/**
 * Ensure compatible version of WordPress is used
 */
if (version_compare('4.7.0', get_bloginfo('version'), '>=')) {
    $sage_error(__('You must be using WordPress 4.7.0 or greater.', 'sage'), __('Invalid WordPress version', 'sage'));
}

/**
 * Ensure dependencies are loaded
 */
if (!class_exists('Roots\\Sage\\Container')) {
    if (!file_exists($composer = __DIR__ . '/../vendor/autoload.php')) {
        $sage_error(
            __('You must run <code>composer install</code> from the Sage directory.', 'sage'),
            __('Autoloader not found.', 'sage')
        );
    }
    require_once $composer;
}

/**
 * Sage required files
 *
 * The mapped array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 */
array_map(function ($file) use ($sage_error) {
    $file = "../app/{$file}.php";
    if (!locate_template($file, true, true)) {
        $sage_error(sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file), 'File not found');
    }
}, ['helpers', 'setup', 'filters', 'admin']);

/**
 * Here's what's happening with these hooks:
 * 1. WordPress initially detects theme in themes/sage/resources
 * 2. Upon activation, we tell WordPress that the theme is actually in themes/sage/resources/views
 * 3. When we call get_template_directory() or get_template_directory_uri(), we point it back to themes/sage/resources
 *
 * We do this so that the Template Hierarchy will look in themes/sage/resources/views for core WordPress themes
 * But functions.php, style.css, and index.php are all still located in themes/sage/resources
 *
 * This is not compatible with the WordPress Customizer theme preview prior to theme activation
 *
 * get_template_directory()   -> /srv/www/example.com/current/web/app/themes/sage/resources
 * get_stylesheet_directory() -> /srv/www/example.com/current/web/app/themes/sage/resources
 * locate_template()
 * ├── STYLESHEETPATH         -> /srv/www/example.com/current/web/app/themes/sage/resources/views
 * └── TEMPLATEPATH           -> /srv/www/example.com/current/web/app/themes/sage/resources
 */
array_map(
    'add_filter',
    ['theme_file_path', 'theme_file_uri', 'parent_theme_file_path', 'parent_theme_file_uri'],
    array_fill(0, 4, 'dirname')
);
Container::getInstance()
    ->bindIf('config', function () {
        return new Config([
            'assets' => require dirname(__DIR__) . '/config/assets.php',
            'theme' => require dirname(__DIR__) . '/config/theme.php',
            'view' => require dirname(__DIR__) . '/config/view.php',
        ]);
    }, true);


function wpse28782_remove_menu_items()
{
    if (!current_user_can('administrator')):
        remove_menu_page('edit.php?post_type=offres');
    endif;
}

add_action('admin_menu', 'wpse28782_remove_menu_items');


function wpse28783_remove_menu_items()
{
    if (!current_user_can('administrator')):
        remove_menu_page('edit.php?post_type=marques');
    endif;
}

add_action('admin_menu', 'wpse28783_remove_menu_items');

function wpse28784_remove_menu_items()
{
    if (!current_user_can('administrator')):
        remove_menu_page('edit.php?post_type=modeles');
    endif;
}

add_action('admin_menu', 'wpse28784_remove_menu_items');

function remove_dashboard_widgets()
{
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);

    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function remove_footer_admin()
{
    echo "";
}

add_filter('admin_footer_text', 'remove_footer_admin');


function admin_css()
{

    $admin_handle = 'admin_css';
    $admin_stylesheet = get_template_directory_uri() . '/assets/styles/admin.css';

    wp_enqueue_style($admin_handle, $admin_stylesheet);
}

add_action('admin_print_styles', 'admin_css', 11);

add_action('wp_ajax_fetch_taxonomy', 'fetch_taxonomy');
add_action('wp_ajax_nopriv_fetch_taxonomy', 'fetch_taxonomy');
function fetch_taxonomy()
{
    $parent = isset($_GET['p']) ? $_GET['p'] : 0;

    $terms = get_terms(array(
        'taxonomy' => 'category_piece',
        'hide_empty' => false,
        'parent' => $parent
    ));
    echo json_encode($terms);
    die();
}

// Récupérer les modèles en fonction d'une requête
add_action('wp_ajax_fetch_models', 'fetch_models');
add_action('wp_ajax_nopriv_fetch_models', 'fetch_models');
function fetch_models()
{
    global $wpdb;

    $request = $_GET['q'];

    $query = 'SELECT ID as id, post_title as text FROM wp_posts WHERE wp_posts.`post_type` = "modeles" AND post_name like "' . $request . '%" AND post_status = "publish"';
    $models = $wpdb->get_results($query);
    $models[] = array("id" => 0, "text" => "Autre");

    echo json_encode(["results" => $models]);
    die();
}

add_action('wp_ajax_fetch_brands', 'fetch_brands');
add_action('wp_ajax_nopriv_fetch_brands', 'fetch_brands');
function fetch_brands()
{
    global $wpdb;

    $request = $_GET['q'];
    $query = 'SELECT ID as id, post_title as text FROM wp_posts WHERE wp_posts.`post_type` = "marques" AND post_name like "' . $request . '%" AND post_status = "publish"';
    $models = $wpdb->get_results($query);

    echo json_encode(["results" => $models]);
    die();
}

function helper_category($parent)
{
    return get_terms(array(
        'taxonomy' => 'category_piece',
        'hide_empty' => false,
        'parent' => $parent
    ));
}

function helper_description($description)
{
    $description = strip_tags($description);
    return (strlen($description) > 140) ? substr($description, 0, 137) . '...' : $description;
}

function paginationBlog($total, $currentPage, $nbPerPage, $path)
{
    $nb_pages = ceil($total / $nbPerPage);

    echo '<div class="switchers" id="switcherArticleList">';
    if ($currentPage !== 1) {
        echo "<a href='" . $path . intval($currentPage - 1) . "'><div class='rect'><img src=" . \App\asset_path('images/img_index/Left_Arrow.png') . " id='arrowRight'></div></a>";
    }

    if (intval($nb_pages) <= 5) {
        for ($n = 1; $n <= $nb_pages; $n++) {
            if ($n === $currentPage) {
                echo '<a href="' . $path . $n . '"><div class="num" id="active">' . $n . '</div></a>';
            } else {
                echo '<a href="' . $path . $n . '"><div class="num">' . $n . '</div></a>';
            }
        }
    }

    if ($currentPage !== intval($nb_pages) && $currentPage !== 1) {
        echo "<a href='" . $path . intval($currentPage + 1) . "'><div class='rect'><img src=" . \App\asset_path('images/img_index/Right_Arrow.png') . " id='arrowRight'></div></a>";
    }
    echo '</div>';
}

function getModelsByLetter($letter)
{
    global $wpdb;

    $query = 'SELECT ID as id, post_title as title FROM wp_posts WHERE wp_posts.`post_type` = "marques" AND post_name like "' . $letter . '%" AND post_status = "publish" ORDER BY title';
    $brands = $wpdb->get_results($query);

    return $brands;
}

function getModelsAlpha()
{
    $brands = array();

    foreach (["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"] as $letter) {
        $brands[$letter] = getModelsByLetter($letter);
    }

    return $brands;
}

/**
 * Search Section
 */

function search($q, $terms, $type, $offset)
{
    global $wpdb;

    if ($terms !== null) {
        $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' AND p.ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN (" . $terms . ")) HAVING (modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '" . $q . "%' AND post_status='publish') OR p.post_title LIKE '%" . $q . "%') AND type='" . $type . "' ORDER BY p.post_date DESC LIMIT 20 OFFSET " . $offset . " ";

        $offers = $wpdb->get_results($query);

        // Results
        $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' AND p.ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id IN (" . $terms . ")) HAVING (modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '" . $q . "%' AND post_status='publish') OR p.post_title LIKE '%" . $q . "%') AND type= '" . $type . "' ORDER BY p.post_date DESC";
    } else {
        $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' HAVING (modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '" . $q . "%' AND post_status='publish') OR p.post_title LIKE '%" . $q . "%') AND type= '" . $type . "' ORDER BY p.post_date DESC LIMIT 20 OFFSET " . $offset . " ";

        $offers = $wpdb->get_results($query);

        // Results
        $query = "SELECT p.ID, p.post_title, (SELECT SUBSTRING(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1), 2, LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value,';',2),':',-1)) - 2) FROM wp_postmeta WHERE post_id=p.ID and meta_key='modele') as modele, (SELECT meta_value FROM wp_postmeta WHERE post_id=p.ID and meta_key='type') as type FROM wp_posts as p WHERE p.post_status='publish' HAVING (modele IN (SELECT ID FROM wp_posts WHERE wp_posts.`post_type`='modeles' AND post_name like '" . $q . "%' AND post_status='publish') OR p.post_title LIKE '%" . $q . "%') AND type= '" . $type . "' ORDER BY p.post_date DESC";
    }

    $nbResults = $wpdb->get_results($query);
    $nbResults = count($nbResults);

    return array(
        "nbResults" => $nbResults,
        "offers" => $offers
    );
}

register_post_status('vendue', array(
    'label' => __('Vendue', 'wp-statuses'),
    'label_count' => _n_noop('Vendue <span class="count">(%s)</span>', 'Vendue <span class="count">(%s)</span>', 'wp-statuses'),
    'public' => true,
    'show_in_admin_all_list' => false,
    'show_in_admin_status_list' => true,
    'post_type' => array('offres'),
    'show_in_metabox_dropdown' => true,
    'show_in_inline_dropdown' => true,
    'show_in_press_this_dropdown' => true,
    'labels' => array(
        'metabox_dropdown' => __('Vendue', 'wp-statuses'),
        'inline_dropdown' => __('Vendue', 'wp-statuses'),
    ),
    'dashicon' => 'dashicons-cart',
));

register_post_status('paiement', array(
    'label' => __('Paiement en cours', 'wp-statuses'),
    'label_count' => _n_noop('Paiement en cours <span class="count">(%s)</span>', 'Paiement en cours <span class="count">(%s)</span>', 'wp-statuses'),
    'public' => false,
    'show_in_admin_all_list' => false,
    'show_in_admin_status_list' => true,
    'post_type' => array('offres'),
    'show_in_metabox_dropdown' => true,
    'show_in_inline_dropdown' => true,
    'show_in_press_this_dropdown' => true,
    'labels' => array(
        'metabox_dropdown' => __('Paiement en cours', 'wp-statuses'),
        'inline_dropdown' => __('Paiement en cours', 'wp-statuses'),
    ),
    'dashicon' => 'dashicons-cart',
));

function sendEmailConfirmationAchat($data)
{

    $mj = new \Mailjet\Client('7ee8d8e4aace5569416c956427a6049c', '29d6107dae3675747d5a4781b6816be6', true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "contact@tacotroc.com",
                    'Name' => "Tacotroc.com"
                ],
                'To' => [
                    [
                        'Email' => "{$data['email']}",
                        'Name' => "{$data['lastname']} {$data['firstname']}"
                    ]
                ],
                'TemplateID' => 1074714,
                'TemplateLanguage' => true,
                'Subject' => "Tacotroc: Confirmation de commande",
                'Variables' => json_decode('{
                "command_id": "' . $data["command_id"] . '",
                "buyer_fullname": "' . $data["lastname"] . ' ' . $data["firstname"] . '",
                "buyer_adress": "' . $data["adress"] . '",
                "buyer_city": "' . $data["city"] . '",
                "buyer_postal": "' . $data["postal"] . '",
                "boyer_state": "' . $data["state"] . '",
                "order_date": "' . $data["date"] . '",
                "offer_title": "' . $data["offer_title"] . '",
                "offer_price": "' . $data["offer_price"] . '"
            }', true)
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
}

function sendEmailConfirmationAnnonce($data)
{

    $mj = new \Mailjet\Client('7ee8d8e4aace5569416c956427a6049c', '29d6107dae3675747d5a4781b6816be6', true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "contact@tacotroc.com",
                    'Name' => "Tacotroc.com"
                ],
                'To' => [
                    [
                        'Email' => "{$data['email']}",
                        'Name' => "{$data['lastname']} {$data['firstname']}"
                    ]
                ],
                'TemplateID' => 1074580,
                'TemplateLanguage' => true,
                'Subject' => "Votre annonce Tacotroc"
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
}

function sendEmailConfirmationPurpose($data)
{

    $mj = new \Mailjet\Client('7ee8d8e4aace5569416c956427a6049c', '29d6107dae3675747d5a4781b6816be6', true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => "contact@tacotroc.com",
                    'Name' => "Tacotroc.com"
                ],
                'To' => [
                    [
                        'Email' => "{$data['email']}",
                        'Name' => "{$data['lastname']} {$data['firstname']}"
                    ]
                ],
                'TemplateID' => 1074580,
                'TemplateLanguage' => true,
                'Subject' => "Votre propositon Tacotroc"
            ]
        ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success();
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function frenchMonth($number)
{
    $month = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

    return $month[intval($number) - 1];
}

function custom_excerpt_more_link($more)
{
    return '...';
}

add_filter('excerpt_more', 'custom_excerpt_more_link');

if (class_exists('WPForms_Template', false)) :
    /**
     * formulaire déclaration de disparation
     * Template for WPForms.
     */
    class WPForms_Template_formulaire_dclaration_de_disparation extends WPForms_Template
    {

        /**
         * Primary class constructor.
         *
         * @since 1.0.0
         */
        public function init()
        {

            // Template name
            $this->name = 'formulaire déclaration de disparation';

            // Template slug
            $this->slug = 'formulaire_dclaration_de_disparation';

            // Template description
            $this->description = '';

            // Template field and settings
            $this->data = array(
                'field_id' => 5,
                'fields' => array(
                    3 => array(
                        'id' => '3',
                        'type' => 'textarea',
                        'label' => 'Circonstances de la disparition',
                        'description' => 'Lieu précis (Adresses, Lieu dit, Parking,.../ Ville/ Pays/ Date / Heure)',
                        'required' => '1',
                        'size' => 'medium',
                    ),
                    4 => array(
                        'id' => '4',
                        'type' => 'checkbox',
                        'label' => 'Cases à cocher',
                        'choices' => array(
                            1 => array(
                                'label' => 'J’ai lu et j’accepte les <span style="text-decoration: underline;"><a href="https://www.tacotroc.com/politique-de-confidentialite.html" title="lire les conditions générales" rel="noopener"> conditions générales d’utilisations </a> </span>',
                            ),
                        ),
                        'required' => '1',
                        'choices_images_style' => 'modern',
                        'label_hide' => '1',
                    ),
                ),
                'settings' => array(
                    'form_title' => 'formulaire déclaration de disparation',
                    'submit_text' => 'ALERTER la communauté',
                    'submit_text_processing' => 'Envoi alerte...',
                    'honeypot' => '1',
                    'notification_enable' => '1',
                    'notifications' => array(
                        1 => array(
                            'email' => '{admin_email}',
                            'subject' => 'Nouvelle entrée pour formulaire déclaration de disparation',
                            'sender_name' => 'Tacotroc',
                            'sender_address' => 'contact@tacotroc.com',
                            'message' => '{all_fields}',
                        ),
                    ),
                    'confirmations' => array(
                        1 => array(
                            'type' => 'message',
                            'message' => '<p>Merci de nous avoir contacté ! Nous vous contacterons rapidement.</p>',
                            'message_scroll' => '1',
                            'page' => '2',
                        ),
                    ),
                ),
                'meta' => array(
                    'template' => 'formulaire_dclaration_de_disparation',
                ),
            );
        }
    }

    new WPForms_Template_formulaire_dclaration_de_disparation;
endif;

if (class_exists('WPForms_Template', false)) :
    /**
     * Enregistrer son véhicule
     * Template for WPForms.
     */
    class WPForms_Template_enregistrer_son_vhicule extends WPForms_Template
    {

        /**
         * Primary class constructor.
         *
         * @since 1.0.0
         */
        public function init()
        {

            // Template name
            $this->name = 'Enregistrer son véhicule';

            // Template slug
            $this->slug = 'enregistrer_son_vhicule';

            // Template description
            $this->description = 'Ce formulaire permet de décrire le véhicule volé et les circonstances de sa disparition.';

            // Template field and settings
            $this->data = array(
                'field_id' => 17,
                'fields' => array(
                    2 => array(
                        'id' => '2',
                        'type' => 'name',
                        'label' => 'Nom                                                                                      Prénom ',
                        'format' => 'first-last',
                        'required' => '1',
                        'size' => 'large',
                        'sublabel_hide' => '1',
                        'css' => ' wpforms-one-half wpforms- wpforms-two-thirds wpforms-firstone-half wpforms-firstwpforms-one-fourth wpforms-first wpforms-one-half wpforms-first',
                    ),
                    6 => array(
                        'id' => '6',
                        'type' => 'email',
                        'label' => 'E-mail',
                        'required' => '1',
                        'size' => 'medium',
                    ),
                    4 => array(
                        'id' => '4',
                        'type' => 'textarea',
                        'label' => 'Votre véhicule à localiser',
                        'description' => 'Description complète du véhicule ( Marque/ Modèle/ Type/ Couleur/ Immatriculation/ Particularité)',
                        'required' => '1',
                        'size' => 'medium',
                    ),
                ),
                'settings' => array(
                    'form_title' => 'Enregistrer son véhicule',
                    'form_desc' => 'Ce formulaire permet de décrire le véhicule volé et les circonstances de sa disparition. ',
                    'submit_text' => 'Enregistrer son véhicule',
                    'submit_text_processing' => 'Envoi immédiat..',
                    'honeypot' => '1',
                    'notification_enable' => '1',
                    'notifications' => array(
                        1 => array(
                            'subject' => 'Formulaire de déclaration de vol',
                            'sender_name' => 'Tacotroc',
                            'sender_address' => 'contact@tacotroc.com',
                            'replyto' => '{admin_email}',
                            'message' => '{all_fields}',
                        ),
                    ),
                    'confirmations' => array(
                        1 => array(
                            'type' => 'message',
                            'message' => '<p>Merci de votre confiance ! Nous activons rapidement l\'alerte disparition et vous enverrons les informations qui nous serons retour<span style="text-decoration: underline;">nées.</span></p>',
                            'message_scroll' => '1',
                            'page' => '2',
                        ),
                    ),
                ),
                'meta' => array(
                    'template' => 'enregistrer_son_vhicule',
                ),
            );
        }
    }

    new WPForms_Template_enregistrer_son_vhicule;
endif;


function load_js_slider()
{
    if (is_page( 36 )){
        wp_enqueue_script('JqueryLR', 'https://code.jquery.com/jquery-3.4.1.min.js');
        wp_enqueue_style('SlickCss-Theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css');
        wp_enqueue_style('SlickCss', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css');
        wp_enqueue_script('SlickJS', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js');
    }
}

add_action('wp_enqueue_scripts','load_js_slider');

function add_endpoint() {
    add_rewrite_endpoint( 'transactions', EP_ALL );
}
add_action( 'init', 'add_endpoint' );
