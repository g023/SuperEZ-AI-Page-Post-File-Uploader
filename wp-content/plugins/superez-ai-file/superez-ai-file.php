<?php
/*
Plugin Name: SuperEZ AI - Page/Post File Uploader
Description: Allow users to upload files to pages or posts.
Version: 0.1a
Author: github.com/g023
License: 3-clause BSD
*/
/*
Wordpress Version tested with: 6.3.2

Really this one works for me at the moment even though it is a bit chaos in the code at the moment.
You can label and upload files to a page or post. 
Uses Wordpress media uploader instead of custom to offload that part of the code, and help to retain compatibility with future versions of Wordpress.
Short tags to display files in page or post to help control display output. Will be adding other variations at some point.
For now, main short tag is: [superez-ai-file]
When you add or remove files, changes are NOT saved until user saves or updates the page or post via the main save/update button on the page/post
Fields of recent uploads will highlight red to indicate that they haven't been saved. They should clear up when you save the page/post
Removing the files from the page/post will not delete that file. That process is handled through the wordpress media manager.
As this is an early build, I'm simply inlining my css and js in the php code. I will be moving that to external files in the future.
Setting file on post/page to private will prevent it from being displayed in the short tag output, but the file is still publically accessible directly.
Setting file on post to private just prevents it from being added to the output of the short tag.
You can add labels to the files, but they are not required. If you don't add a label, the short tag will use the shortname of the file as the label.
The shortname of a file is the name of the file after the last slash in the url. The filename is then shrunk down to 12 characters and the middle is replaced with ...
Everything is dynamic, so no janky refreshes or anything like that.
2 templates are used to control the output of the short tag. You can edit them in the code. I will be adding a settings page to control this in the future.
The templates are in a global var $tpl at the moment and stored in $tpl['main'] and $tpl['rows']
Might actually make this whole thing a class to prevent conflicts here. Guess we'll see.

The media uploader only works for admin users (set in the code).
*/

// now for the admin view

// class is broken for now. will fix later.
// include_once(__DIR__.'/class.super_ez_ai_pagepost_file_uploader.php');
// $superez_ai_file_uploader = new super_ez_ai_pagepost_file_uploader();


// now for the admin view



// Enqueue JavaScript and CSS
function enqueue_plugin_assets() {
    // wp_enqueue_script('multiple-file-upload-plugin-script', plugin_dir_url(__FILE__) . 'js/multiple-file-upload.js', array('jquery', 'media-upload', 'thickbox'), '1.0', true);
    // wp_enqueue_style('multiple-file-upload-plugin-style', plugin_dir_url(__FILE__) . 'css/multiple-file-upload.css');
}


// Add a custom meta box to the post and page editor
function add_file_upload_meta_box() {
    add_meta_box('multiple-file-upload-meta-box', 'SuperEZ AI - Page/Post File Uploader', 'render_file_upload_meta_box', 'post', 'normal', 'high');
    add_meta_box('multiple-file-upload-meta-box', 'SuperEZ AI - Page/Post File Uploader', 'render_file_upload_meta_box', 'page', 'normal', 'high');
}


// Render the custom meta box
function render_file_upload_meta_box($post) {

    // our main arrays: uploaded_files, uploaded_files_labels, uploaded_vis, uploaded_files_tags

    // if we are not admin, dont show the upload form
    if (!current_user_can('manage_options')) {
        echo '<p>You do not have permission to upload files.</p>';
        return;
    }

    // Use get_post_meta to retrieve an existing value from the database.
    $uploaded_files = get_post_meta($post->ID, 'uploaded_files', true);

    $uploaded_files_labels = get_post_meta($post->ID, 'uploaded_files_labels', true);

    // get cur selected
    $uploaded_vis = get_post_meta($post->ID, 'uploaded_vis', true);

    // Display the form, using the current value.
    ?>
    <div class='details'>
        <div class="inside">
            <p>Upload one or more files that will be associated with this post/page.</p>
            <p>use short tags <b>[superez-ai-file]</b> to access files in post or page. Uploads will not be attached fully until page or post is saved/updated.</p>
        </div>
    </div>
    <div id="file-upload-container">
        <ul>
            <?php 
            
            $count = 0;
            if (is_array($uploaded_files)){

                foreach ($uploaded_files as $file) {
                    if (empty($file)) continue;

                    if(is_array($uploaded_files_labels) && isset($uploaded_files_labels[$count]))
                        $file_labels = $uploaded_files_labels[$count];
                    else // use shortname after last slash on url
                        $file_labels = substr($file, strrpos($file, '/') + 1);
                    
                    // get cur selected
                    if(is_array($uploaded_vis) && isset($uploaded_vis[$count]) && !empty($uploaded_vis[$count]))
                        $file_vis = $uploaded_vis[$count];
                    else // use shortname after last slash on url
                        $file_vis = 'private';

                    ?>
                    <li>
                        <!-- visibility -->
                        <select name="uploaded_vis[]" class="uploaded_vis">
                            <option value="public" <?php if($file_vis == 'public') echo 'selected'; ?>>Public</option>
                            <option value="private" <?php if($file_vis == 'private') echo 'selected'; ?>>Private</option>
                        </select>
                        <!-- end visibility -->

                        <input type="text" name="uploaded_files_labels[]" value="<?php echo $file_labels; ?>" />
                        <input type="text" name="uploaded_files[]" value="<?php echo $file; ?>" class="disabled"  /><!-- cant set them disabled as attr or no save -->

                        <a class='download-file' href="<?php echo $file; ?>" download>Download</a>

                        <a href="#" class="remove-file">Remove</a><!-- note: add code to remove file from server in this script at the end -->
                    </li>
                    <?php 
                    $count++;
                }
            }
            ?>
        </ul>
    </div>

    <input type="button" class="button button-primary" value="Upload File(s)" id="upload_file_button" />
    <p>Upload one or more files that will be associated with this post/page.</p>

    <?php
}

function ez_safe_text($str)
{
    $san = sanitize_text_field($str);
    return $san;
}

function ez_update_or_delete_meta($post_id, $meta_key, $meta_value)
{
    if (isset($_POST[$meta_key]))
    {
        // sanitize
        $meta_value = array_map('ez_safe_text', $meta_value);
        // update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
        update_post_meta($post_id, $meta_key, $meta_value);
    }
    else 
        delete_post_meta($post_id, $meta_key);
}

// Save the uploaded files when the post or page is saved
function save_uploaded_files($post_id) {
    // this is the admin side view in the page or post editor.

    // if we are not admin return
    if (!current_user_can('manage_options')) return;
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    if (!current_user_can('edit_post', $post_id)) return;

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) return;
    } else {
        if (!current_user_can('edit_post', $post_id)) return;
    }

    // update or delete meta - uploaded_files, uploaded_files_labels, uploaded_vis, uploaded_files_tags
    ez_update_or_delete_meta($post_id, 'uploaded_files',        @$_POST['uploaded_files']);
    ez_update_or_delete_meta($post_id, 'uploaded_files_labels', @$_POST['uploaded_files_labels']);
    ez_update_or_delete_meta($post_id, 'uploaded_vis',          @$_POST['uploaded_vis']);
    ez_update_or_delete_meta($post_id, 'uploaded_files_tags',   @$_POST['uploaded_files_tags']);
}


// Include JavaScript to handle the file uploads
function include_upload_file_script() {
    // if we are not admin, dont show the upload form
    if (!current_user_can('manage_options')) 
        return;
    ?>
    <style>
        .unsaved {
            background-color: #ffcccc;
        }
    </style>
    <script>
        jQuery(document).ready(function ($) {
            jQuery(document).on('click', '.editor-post-publish-button', function() {
                // removed all .unsaved classes in document
                jQuery('.unsaved').each(function(){
                    jQuery(this).removeClass('unsaved');
                });
            }); // handle save/update button click


            // TODO: work with the update draft button
        });
    </script>
    <script>
        jQuery(document).ready(function ($) {
            jQuery('#upload_file_button').click(function (e) {
                e.preventDefault();
                var file_frame;
                if (file_frame) {
                    file_frame.open();
                    return;
                }

                file_frame = wp.media.frames.file_frame = wp.media({
                    title: 'Select or Upload Files',
                    button: {
                        text: 'Use these files'
                    },
                    multiple: true // Allow multiple file selection
                });

                file_frame.on('select', function () {
                    var attachments = file_frame.state().get('selection').toJSON();

                    jQuery.each(attachments, function (i, attachment) {
                        var fileVis_html = '<select name="uploaded_vis[]" class="uploaded_vis"><option value="public">Public</option><option value="private" selected>Private</option></select>';

                        var fileInput = jQuery('<input>').attr('type', 'text').attr('name', 'uploaded_files[]').val(attachment.url).addClass('disabled');
                        
                        // add label
                        var fileLabel = jQuery('<input>').attr('type', 'text').attr('name', 'uploaded_files_labels[]').val(attachment.filename);

                        jQuery('#file-upload-container ul')
                            .append(jQuery('<li class="unsaved">')
                            .append(fileVis_html)
                            .append(' ')
                            .append(fileLabel)
                            .append(' ')
                            .append(fileInput)
                            .append(' ')
                            .append(jQuery('<a>').attr('href', attachment.url).attr('download', '').addClass('download-file').text('Download'))
                            .append(' ')
                            .append(jQuery('<a>').attr('href', '#').addClass('remove-file').text('Remove')));
                    }); // end each
                }); // end select

                // Now display the actual file_frame
                file_frame.open();
            }); // end click (upload_file_button)
        }); // end document ready

        // Remove file from list
        jQuery(document).ready(function ($) {
            jQuery('#file-upload-container').on('click', '.remove-file', function (e) {
                e.preventDefault();
                jQuery(this).parent().remove();
            });
        });

    </script>
    <?php
} // end: include_upload_file_script


add_action('admin_enqueue_scripts', 'enqueue_plugin_assets');
add_action('add_meta_boxes', 'add_file_upload_meta_box');
add_action('save_post', 'save_uploaded_files');
add_action('admin_footer', 'include_upload_file_script');


//$superez_ai_file_uploader = new SuperEZAIFileUploader();



// END : admin view

// BEGIN :: PUBLIC FACING VIEW
// include class.handle_shorttags.php in /_inc/
include_once(__DIR__.'/class.handle_shorttags.php');

$s_handle_shorttags = new superez_ai_handle_shorttags();

add_action('wp_head', array($s_handle_shorttags, 'footer_styles'));
add_shortcode('superez-ai-file', array($s_handle_shorttags, 'process_templates_html'));
// END :: PUBLIC FACING VIEW

// some helpful snippets:

/*
// access a class method with a wordpress action
class MyClass {
    public function myMethod() {
        // Your custom code goes here
        echo "Hello from MyClass::myMethod!";
    }
}

$myInstance = new MyClass();

add_action('wp_head', array($myInstance, 'myMethod'));
*/