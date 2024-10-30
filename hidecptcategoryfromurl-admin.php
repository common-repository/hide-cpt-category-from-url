<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
// Check permissions
if (!current_user_can('administrator'))
    return;
if (isset($_POST['save']) && check_admin_referer('post_id_' . get_the_ID(), 'post-id')) {
    $hidecptc_post_title = sanitize_meta("post_title", $_POST['post_title'], "post");
    $hidecptc_cpt_categories_titles = "";
    $hidecptc_cpt_categories_titles = ($hidecptc_post_title) ? $hidecptc_post_title : $hidecptc_cpt_categories;
    $collabetitles = implode(",", $hidecptc_cpt_categories_titles);
    if ($collabetitles) {
        update_option('cptcategories', str_replace(" ", "", $collabetitles));
    } else {
        add_option('cptcategories', $collabetitles);
    }
}
$hidecptc_get_all_CPT = hidecptc_get_all_CPT();
$hidecptc_cpt_categories = hidecptc_categories_name();
?>
<div class="wrap">
    <h1>Select all CPT's</h1>
    Select all the CPT's (custom post type) slug that you want to hide.
    <form name="hidecptcategory" action="#" method="post" id="hidecptcategory">
        <div id="titlediv">
            <br>
            <?php
            if ($hidecptc_get_all_CPT) {
                wp_nonce_field('post_id_' . get_the_ID(), "post-id");
                ?>
                <select multiple name="post_title[]">
                    <?php
                    foreach ($hidecptc_get_all_CPT as $hidecptc_post_type) {
                        ?>
                        <option value="<?php echo esc_html($hidecptc_post_type); ?>" <?php if (in_array($hidecptc_post_type, $hidecptc_cpt_categories)) echo "selected"; ?>><?php echo esc_html($hidecptc_post_type); ?></option>
                        <?php
                    }
                    ?>
                </select>
                <?php
            }
            ?>
            <br><br>
            <input type="submit" name="save" id="save"
                   class="button button-primary button-large pull-right" value="Save">
        </div>
    </form>
    <?php
    if (isset($_POST['save']) && check_admin_referer('post_id_' . get_the_ID(), 'post-id')) {
        echo "<h3>Details saved</h3>";
    }
    ?>
</div>