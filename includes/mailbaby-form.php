<?php
if( !defined('ABSPATH') )
{
      die('You cannot be here');
}

add_shortcode('mailbaby', 'show_mailbaby_form');

add_action( 'rest_api_init', 'create_rest_endpoint'); 

add_action( 'init', 'create_submissions_page');

add_action('add_meta_boxes', 'create_meta_box');

add_filter('manage_submission_posts_columns', 'custom_submission_columns');

add_action('manage_submission_posts_custom_column', 'fill_submission_columns', 10, 2);

add_action('admin_init', 'setup_search');

add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

// add_action('wp_head', 'add_shortcode_to_header');

// function add_shortcode_to_header() {
//     // Add the shortcode to the header
//     echo do_shortcode('[mailbaby]');
// }


function enqueue_custom_scripts()
{

    // Enqueue custom CSS for plugin
    wp_enqueue_style('mailbaby-plugin-css', MY_PLUGIN_URL . 'assets/css/mailbaby-plugin.css');

    // Enqueue custom JavaScript for plugin
    wp_enqueue_script('mailbaby-plugin-js', MY_PLUGIN_URL . 'assets/js/custom-script.js', array('jquery'), null, true);
}



function setup_search()
{

      // Only apply filter to submissions page

      global $typenow;

      if ($typenow === 'submission') {

            add_filter('posts_search', 'submission_search_override', 10, 2);
      }
}

function submission_search_override($search, $query)
{
      // Override the submissions page search to include custom meta data

      global $wpdb;

      if ($query->is_main_query() && !empty($query->query['s'])) {
            $sql    = "
              or exists (
                  select * from {$wpdb->postmeta} where post_id={$wpdb->posts}.ID
                  and meta_key in ('name','email')
                  and meta_value like %s
              )
          ";
            $like   = '%' . $wpdb->esc_like($query->query['s']) . '%';
            $search = preg_replace(
                  "#\({$wpdb->posts}.post_title LIKE [^)]+\)\K#",
                  $wpdb->prepare($sql, $like),
                  $search
            );
      }

      return $search;
}

function fill_submission_columns($column, $post_id)
{
      // Return meta data for individual posts on table

      switch ($column) {

            case 'name':
                  echo esc_html(get_post_meta($post_id, 'name', true));
                  break;

            case 'email':
                  echo esc_html(get_post_meta($post_id, 'email', true));
                  break;

      }
}

function custom_submission_columns($columns)
{
      // Edit the columns for the submission table

      $columns = array(

            'cb' => $columns['cb'],
            'name' => __('Name', 'mailbaby-plugin'),
            'email' => __('Email', 'mailbaby-plugin')

      );

      return $columns;
}

function create_meta_box()
{
      // Create custom meta box to display submission

      add_meta_box('custom_mailbaby_form', 'Submission', 'display_submission', 'submission');
}

function display_submission()
{
//  Display individual submission data on it's page

      $postmetas = get_post_meta( get_the_ID() );
      unset($postmetas['_edit_lock']);
      unset($postmetas['_wp_http_referer']);
      unset($postmetas['rest_route']);

      echo '<ul>';

      foreach($postmetas as $key => $value)
      {

            echo '<li><strong>' . $key . ':</strong> ' . $value[0] . '</li>';

      }

      echo '</ul>';



}

function create_submissions_page()
{

      // Create the submissions post type to store form submissions

      $args = [

            'public' => true,
            'has_archive' => true,
            'menu_position' => 30,
            'publicly_queryable' => false,
            'labels' => [

                  'name' => 'MailBaby Submissions',
                  'singular_name' => 'Submission',
                  'edit_item' => 'View Submission'

            ],
            'supports' => false,
            'capability_type' => 'post',
            'capabilities' => array(
                  'create_posts' => false,
            ),
            'map_meta_cap' => true,
            'menu_icon' => 'dashicons-reddit'
      ];

      register_post_type('submission', $args);
}


function show_mailbaby_form()
{
    include MY_PLUGIN_PATH . 'includes/templates/mailbaby-form.php';
}

function create_rest_endpoint()
{
    register_rest_route('v1/mailbaby-form', 'submit', array(

            'methods' => 'POST',
            'callback' => 'handle_enquiry'

    ));
}

function handle_enquiry($data)
{
    $params = $data->get_params();

    // Set fields from the form
    $field_name = sanitize_text_field($params['name']);
    $field_email = sanitize_email($params['email']);

    if (!wp_verify_nonce($params['_wpnonce'], 'wp_rest')) {
        return new WP_Rest_Response('Message not sent. Nonce verification failed.', 422);
    }

    // Remove unnecessary parameters
    unset($params['_wpnonce']);
    unset($params['_wp_http_referer']);

    // Send the email message
    $headers = [];

    $admin_email = get_bloginfo("admin_email");
    $admin_name = get_bloginfo("name");

         // Set recipient email
         $recipient_email = get_plugin_options('mailbaby_plugin_recipients');

         if (!$recipient_email) {
               // Make all lower case and trim out white space
               $recipient_email = strtolower(trim($recipient_email));
         } else {
   
               // Set admin email as recipient email if no option has been set
               $recipient_email = $admin_email;
         }

         
    $headers[] = "From: {$admin_name} <{$admin_email}>";
    $headers[] = "Reply-to: {$params['name']} <{$params['email']}>";
    $headers[] = "Content-type: text/html";

    $subject = "New enquiry from {$params['name']}";
    $message .= "<h1>Message has been sent from {$params['name']}</h1><br><br>";

    $postarr = [

      'post_title' => $params['name'],
      'post_type' => 'submission',
      'post_status' => 'publish'

];

$post_id = wp_insert_post($postarr);

// Loop through each field posted and sanitize it
foreach ($params as $label => $value) {

      switch ($label) {

            case 'name':

                  $value = sanitize_textarea_field($value);
                  break;

            case 'email':

                  $value = sanitize_email($value);
                  break;

            default:

                  $value = sanitize_text_field($value);
      }

      add_post_meta($post_id, sanitize_text_field($label), $value);

      $message .= '<strong>' . sanitize_text_field(ucfirst($label)) . ':</strong> ' . $value . '<br />';
}

    wp_mail($recipient_email, $subject, $message, $headers);
      
    $confirmation_message = "The message was sent successfully!!";

    if (get_plugin_options('mailbaby_plugin_message')) {

          $confirmation_message = get_plugin_options('mailbaby_plugin_message');

          $confirmation_message = str_replace('{name}', $params['name'], $confirmation_message);
    }

    return new WP_Rest_Response($confirmation_message, 200 );
}
