<?php
/*
    file: class.handle_shorttags.php
    class to handle the shortcodes for this plugin
    description: a helper class for the superez-ai-file plugin to process the public facing template that shows on blog or post pages

    note: must be in same folder as our main plugin file (superez-ai-file.php)

    license: 3-clause BSD
*/
class superez_ai_handle_shorttags
{
    public $templates = []; // main, row

    public function __construct($tfile_style='template.style.css', $tfile_main='template.main.html', $tfile_row='template.row.html')
    {
        $dir = __DIR__.'/_inc/';
        $this->templates['style'] = file_get_contents($dir.$tfile_style); // [style
        $this->templates['main'] = file_get_contents($dir.$tfile_main);
        $this->templates['rows'] = file_get_contents($dir.$tfile_row); 
    }

    public function footer_styles()
    {
        $html = "<!-- sez css --><style>".$this->templates['style']."</style>";
        echo $html;
    } // end: footer_styles


    function get_public_rows()
    {
        // get the list of files attached to the page or post
        $uploaded_files         = get_post_meta(get_the_ID(), 'uploaded_files', true);
        $uploaded_files_labels  = get_post_meta(get_the_ID(), 'uploaded_files_labels', true);
        $uploaded_vis           = get_post_meta(get_the_ID(), 'uploaded_vis', true);
    
        // combine all the different vars into row[] = ['fld'=>'val',...]
        $c = 0;
        $rows = [];
        if(is_array($uploaded_files)){
            foreach($uploaded_files as $file){
                $label  = $uploaded_files_labels[$c];
                $vis    = $uploaded_vis[$c];
    
                // we only want to show public entries
                if((!empty($vis) && $vis != 'public') || empty($file)){
                    $c++;
                    continue;
                }
    
                $rows[$c]['file'] = $file;
                if(!empty($label))
                    $rows[$c]['label'] = $label;
                else
                    $rows[$c]['label'] = 'file';
    
                $c++;
            }
        }
    
        return $rows;
    } // end: get_public_rows

    function process_templates_html()
    {
        /*
        process the templates for the public facing view ( the actual page or post ):
        from this wordpress plugin's directory
        in a folder called _inc/
        a file called template.main.php and template.row.php
        */
        $rows = $this->get_public_rows();
        $tpl_main = $this->templates['main'];
        $tpl_rows = $this->templates['rows'];
    
        $html = $tpl_main; // 
    
        // build the rows template
        $html_rows = '';
        foreach($rows as $row){
            $html_row = $tpl_rows;
            $tags = [
                '{{{FILE_LABEL}}}' => $row['label'],
                '{{{FILE_SHORTNAME}}}' => substr($row['file'], strrpos($row['file'], '/') + 1),
                '{{{FILE_URL}}}' => $row['file'],
            ];
    
            // replace our tags in the row template {{{TAG_TAG}}} */
            foreach($tags as $tag=>$val)
                $html_row = str_replace($tag, $val, $html_row);
            
            $html_rows .= $html_row;
        } // end rows loop (build the rows template)
    
        // now build the main template
        // place our freshly built rows template ($html_rows) and plant it where we want it in the main template {{{TEMPLATE_ROW}}}
        $html = str_replace('{{{TEMPLATE_ROW}}}', $html_rows, $html);
    
        return $html; // pass back the processed template
    } // end: handle_template_tags

} // end: superez_ai_handle_shorttags

?>