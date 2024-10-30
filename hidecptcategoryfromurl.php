<?php
/*
Plugin Name: Hide CPT category from URL
Plugin URI:  https://www.imskh.com/projects/hidecptcategoryfromurl.zip
Description: This plugin helps in hiding custom post type category from the url and only shows the actual page name.
Version:     1.0.0
Author:      Shashank Kumar
Author URI:  https://www.imskh.com
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: hide-cpt-category-from-url
Domain Path: /languages
*/
if (!defined('ABSPATH')) exit; // Exit if accessed directly
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * Register a custom menu page.
 */
function hidecptc_register_menu_page()
{
    add_menu_page(
        __('Hide CPT Category', 'textdomain'),
        'Hide CPT Categories',
        'manage_options',
        'hide-cpt-category-from-url/hidecptcategoryfromurl-admin.php',
        '',
        'dashicons-hidden',
        5
    );
}

add_action('admin_menu', 'hidecptc_register_menu_page');
/**
 * Actual code that hide category from the URL. Categories are dynamically taken from the user from wordpress settings
 *
 * @param $hidecptc_post_link
 * @param $hidecptc_post
 *
 * @return mixed
 */
function hidecptc_remove_slug($hidecptc_post_link, $hidecptc_post)
{
    $hidecptc_all_categories = hidecptc_categories_name();
    foreach ($hidecptc_all_categories as $hidecptc_cur_category) {
        if ($hidecptc_cur_category === $hidecptc_post->post_type && 'publish' === $hidecptc_post->post_status) {
            $hidecptc_post_link = str_replace('/' . trim($hidecptc_cur_category) . '/', '/', $hidecptc_post_link);
        }
    }

    return $hidecptc_post_link;
}

add_filter('post_type_link', 'hidecptc_remove_slug', 10, 2);
/**
 * Helper function that helps main/above function to hide the Category from the URL
 * @param $hidecptc_query
 */
function hidecptc_add_cpt_post_names_to_main_query($hidecptc_query)
{
    // Bail if this is not the main hidecptc_query.
    if (!$hidecptc_query->is_main_query()) {
        return;
    }
    // Bail if this hidecptc_query doesn't match our very specific rewrite rule.
    if (!isset($hidecptc_query->query['page']) || 2 !== count($hidecptc_query->query)) {
        return;
    }
    // Bail if we're not querying based on the post name.
    if (empty($hidecptc_query->query['name'])) {
        return;
    }
    $hidecptc_categories_to_hide = hidecptc_categories_name();
    $hidecptc_categories_to_hide[] = "post";
    $hidecptc_categories_to_hide[] = "page";
    // Add CPT to the list of post types WP will include when it queries based on the post name.
    $hidecptc_query->set('post_type', $hidecptc_categories_to_hide);
}

add_action('pre_get_posts', 'hidecptc_add_cpt_post_names_to_main_query');
/**
 * Returns all custom post type categories name in array format
 * @return array
 */
function hidecptc_categories_name()
{
    $hidecptc_cpt_categories = get_option('cptcategories');

    return explode(",", $hidecptc_cpt_categories);
}

/**
 * Get and return all custom post type
 * @return array
 */
function hidecptc_get_all_CPT()
{
    $hidecptc_args = array(
        'public' => true,
        '_builtin' => false
    );
    $hidecptc_output = 'names'; // names or objects, note names is the default
    $hidecptc_operator = 'and'; // 'and' or 'or'
    $hidecptc_post_types = get_post_types($hidecptc_args, $hidecptc_output, $hidecptc_operator);
    return $hidecptc_post_types;
}