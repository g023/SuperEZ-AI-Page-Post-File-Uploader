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
include_once(__DIR__.'/class.super_ez_ai_pagepost_file_uploader.php');

$superez_ai_file_uploader = new super_ez_ai_pagepost_file_uploader();

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