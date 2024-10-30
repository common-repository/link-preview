<?php
/*
  Plugin Name: Preview
  Plugin URI: http://ajaxtown.com/
  Description: A plugin used to embed preview of a link similar to facebook
  Version: 1.0
  Author: Abhishek Saha
  Author URI: http://ajaxtown.com
  License: GPL
 */

add_action('wp_head', 'preview');

function preview() {
    //echo "css can be added here..";
    echo '<style type="text/css">';
    echo get_option('preview_css');
    echo '</style>';
}

add_action( 'admin_head', 'at_preview_add_tinymce' );

function at_preview_add_tinymce() {
    
    global $typenow;

    // only on Post Type: post and page
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return ;
    
    
    
    add_filter( 'mce_external_plugins', 'at_preview_add_tinymce_plugin' );
    // Add to line 1 form WP TinyMCE
    add_filter( 'mce_buttons', 'at_preview_add_tinymce_button' );
}
// inlcude the js for tinymce
function at_preview_add_tinymce_plugin( $plugin_array ) {

    $plugin_array['at_preview'] = plugins_url( '/plugin.js', __FILE__ );
    return $plugin_array;
}

// Add the button key for address via JS
function at_preview_add_tinymce_button( $buttons ) {

    array_push( $buttons, 'at_preview_button_key' );
    //echo get_option('preview_css');
    return $buttons;
}

/* Runs on plugin activation */
register_activation_hook(__FILE__, 'preview_install');

/* Runs on plugin deactivation */
register_deactivation_hook(__FILE__, 'preview_remove');

function preview_install() {
    /* Creates new database field */
    add_option("preview_css", get_preview_css(), '', 'yes');
}

function preview_remove() {
    /* Deletes the database field */
    delete_option('preview_css');
}

function get_preview_css() {
    return '#at_preview .preview_footer {
                background: #fcfcfc !important;
                font-size: 70% !important;
            }
            #at_preview .preview_footer a,#at_preview .preview_footer {
                text-decoration: none !important;
                text-transform: uppercase !important;
            }';
}


if (is_admin()) {

    /* Call the html code */
    add_action('admin_menu', 'preview_admin_menu');

    function preview_admin_menu() {
        add_options_page('Preview', 'Preview', 'administrator', 'hello-world', 'preview_option_page');
    }

}


add_filter('tiny_mce_before_init', 'wpse24113_tiny_mce_before_init');

function wpse24113_tiny_mce_before_init($initArray) {
    $initArray['setup'] = <<<JS
[function(ed) {
    ed.onKeyDown.add(function(ed, e) {
        //your function goes here
        console.debug('Key down event: ' + e.keyCode);
    });

}][0]
JS;
    return $initArray;
}

function preview_option_page() {
    ?>
    <div>
        <h2>Ajaxtown - Preview Options</h2>
        <hr>
        <form method="post" action="options.php">
            <?php wp_nonce_field('update-options'); ?>

            <table width="510">
                <tr valign="top">
                    <td width="92" scope="row">Ajaxtown - Preview CSS</td>
                </tr>
                <tr valign="top">
                    <td width="406">
                        <textarea name="preview_css" id="preview_css" rows="10" cols="70"><?php echo get_option('preview_css'); ?></textarea>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="preview_css" />

            <p>
                <input type="submit" value="<?php _e('Save Changes') ?>" />
            </p>

        </form>
    </div>
    <?php
}
?>