<?php

class CP_ContactFormToEmail extends CP_CFTEMAIL_BaseClass {

    private $menu_parameter = 'cp_contactformtoemail';
    private $prefix = 'cp_contactformtoemail';
    private $plugin_name = 'Contact Form to Email';
    private $plugin_URL = 'https://form2email.dwbooster.com';
    public $table_items = "cftemail_forms";
    public $table_messages = "cftemail_messages";
    public $print_counter = 1;
    private $include_user_data_csv = false;

    public $shorttag = 'CONTACT_FORM_TO_EMAIL';
    
    protected $tags_allowed = array(
                                  'a' => array(
                                      'href' => array(),
                                      'title' => array(),
                                      'style' => array(),
                                      'class' => array(),
                                  ),
                                  'br' => array(),
                                  'em' => array(),
                                  'b' => array(),
                                  'strong' => array(),
                                  'img' => array(
                                            'src' => array(),
                                            'width' => array(),
                                            'height' => array(),
                                            'border' => array(),
                                            'style' => array(),
                                            'class' => array(),
                                            ),
                              );    

    function _install() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charset_collate = $wpdb->get_charset_collate();

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_messages."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_messages." (
                id int(10) NOT NULL AUTO_INCREMENT,
                formid INT NOT NULL,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                ipaddr VARCHAR(250) DEFAULT '' NOT NULL,
                notifyto VARCHAR(250) DEFAULT '' NOT NULL,
                data mediumtext,
                posted_data mediumtext,
                UNIQUE KEY id (id)
            ) ".$charset_collate.";";
            $wpdb->query($sql);
        }

        $results = $wpdb->get_results("SHOW TABLES LIKE '".$wpdb->prefix.$this->table_items."'");
        if (!count($results))
        {
            $sql = "CREATE TABLE ".$wpdb->prefix.$this->table_items." (
                 id mediumint(9) NOT NULL AUTO_INCREMENT,

                 form_name VARCHAR(250) DEFAULT '' NOT NULL,

                 form_structure mediumtext,

                 fp_from_email VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_destination_emails text,
                 fp_subject text,
                 fp_inc_additional_info VARCHAR(20) DEFAULT '' NOT NULL,
                 fp_return_page VARCHAR(250) DEFAULT '' NOT NULL,
                 fp_message text,
                 fp_emailformat VARCHAR(20) DEFAULT '' NOT NULL,

                 cu_enable_copy_to_user VARCHAR(10) DEFAULT '' NOT NULL,
                 cu_user_email_field VARCHAR(250) DEFAULT '' NOT NULL,
                 cu_subject text,
                 cu_message text,
                 cu_emailformat VARCHAR(20) DEFAULT '' NOT NULL,
                 fp_emailfrommethod VARCHAR(20) DEFAULT '' NOT NULL,

                 fp_enableemail VARCHAR(10) DEFAULT '' NOT NULL,
                 onsubmitaction VARCHAR(10) DEFAULT '' NOT NULL,
                 fp_return_message text,

                 vs_use_validation text,
                 vs_text_is_required text,
                 vs_text_is_email text,
                 vs_text_datemmddyyyy text,
                 vs_text_dateddmmyyyy text,
                 vs_text_number text,
                 vs_text_digits text,
                 vs_text_max text,
                 vs_text_min text,
                 vs_text_page text,
                 vs_text_of text,
                 vs_text_submitbtn text,
                 vs_text_previousbtn text,
                 vs_text_nextbtn text,

                 rep_enable VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_days VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_hour VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_emails text,
                 rep_subject text,
                 rep_emailformat VARCHAR(10) DEFAULT '' NOT NULL,
                 rep_message text,

                 cv_enable_captcha VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_enable_captchaintelligent VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_width VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_height VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_chars VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_font VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_min_font_size VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_max_font_size VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_noise VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_noise_length VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_background VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_border VARCHAR(20) DEFAULT '' NOT NULL,
                 cv_text_enter_valid_captcha text,

                 UNIQUE KEY id (id)
            ) ".$charset_collate.";";
            $wpdb->query($sql);
        }

        // insert initial data
        $count = $wpdb->get_var(  "SELECT COUNT(id) FROM ".$wpdb->prefix.$this->table_items  );
        if (!$count)
        {
            $default_from = strtolower(get_the_author_meta('user_email', get_current_user_id()));
            $domain = str_replace('www.','', strtolower($_SERVER["HTTP_HOST"]));                                  
            while (substr_count($domain,".") > 1)
                $domain = substr($domain, strpos($domain, ".")+1);                
            $pos = strpos($default_from, $domain);
            if (substr_count($domain,".") == 1 && $pos === false)
                define('CP_CFEMAIL_DEFAULT_fp_from_email', 'admin@'.$domain );
            else    
                define('CP_CFEMAIL_DEFAULT_fp_from_email', $default_from );
            
            
            define('CP_CFEMAIL_DEFAULT_fp_destination_emails', get_the_author_meta('user_email', get_current_user_id()) );
            $wpdb->insert( $wpdb->prefix.$this->table_items, array( 'id' => 1,
                                      'form_name' => 'Form 1',

                                      'form_structure' => $this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure),

                                      'fp_from_email' => $this->get_option('fp_from_email', CP_CFEMAIL_DEFAULT_fp_from_email),
                                      'fp_destination_emails' => $this->get_option('fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_destination_emails),
                                      'fp_subject' => $this->get_option('fp_subject', CP_CFEMAIL_DEFAULT_fp_subject),
                                      'fp_inc_additional_info' => $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info),
                                      'fp_return_page' => $this->get_option('fp_return_page', CP_CFEMAIL_DEFAULT_fp_return_page),
                                      'fp_message' => $this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message),
                                      'fp_emailformat' => $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format),

                                      'cu_enable_copy_to_user' => $this->get_option('cu_enable_copy_to_user', CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user),
                                      'cu_user_email_field' => $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field),
                                      'cu_subject' => $this->get_option('cu_subject', CP_CFEMAIL_DEFAULT_cu_subject),
                                      'cu_message' => $this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message),
                                      'cu_emailformat' => $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format),

                                      'fp_return_message' => 'Your message has been sent. Thank you!',
                                      'onsubmitaction' => '3',
                                      'fp_enableemail' => 'true',

                                      'vs_use_validation' => $this->get_option('vs_use_validation', CP_CFEMAIL_DEFAULT_vs_use_validation),
                                      'vs_text_is_required' => $this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required),
                                      'vs_text_is_email' => $this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email),
                                      'vs_text_datemmddyyyy' => $this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy),
                                      'vs_text_dateddmmyyyy' => $this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy),
                                      'vs_text_number' => $this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number),
                                      'vs_text_digits' => $this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits),
                                      'vs_text_max' => $this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max),
                                      'vs_text_min' => $this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min),
                                      'vs_text_page' => $this->get_option('vs_text_page', 'Page'),
                                      'vs_text_of' => $this->get_option('vs_text_of', 'of'),
                                      'vs_text_submitbtn' => $this->get_option('vs_text_submitbtn', 'Submit'),
                                      'vs_text_previousbtn' => $this->get_option('vs_text_previousbtn', 'Previous'),
                                      'vs_text_nextbtn' => $this->get_option('vs_text_nextbtn', 'Next'),

                                      'rep_enable' => $this->get_option('rep_enable', 'no'),
                                      'rep_days' => $this->get_option('rep_days', '1'),
                                      'rep_hour' => $this->get_option('rep_hour', '0'),
                                      'rep_emails' => $this->get_option('rep_emails', ''),
                                      'rep_subject' => $this->get_option('rep_subject', 'Submissions report...'),
                                      'rep_emailformat' => $this->get_option('rep_emailformat', 'text'),
                                      'rep_message' => $this->get_option('rep_message', 'Attached you will find the data with the form submissions.'),

                                      'cv_enable_captcha' => $this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha),
                                      'cv_enable_captchaintelligent' => $this->get_option('cv_enable_captchaintelligent', CP_CFEMAIL_DEFAULT_cv_enable_captchainvisible),
                                      'cv_width' => $this->get_option('cv_width', CP_CFEMAIL_DEFAULT_cv_width),
                                      'cv_height' => $this->get_option('cv_height', CP_CFEMAIL_DEFAULT_cv_height),
                                      'cv_chars' => $this->get_option('cv_chars', CP_CFEMAIL_DEFAULT_cv_chars),
                                      'cv_font' => $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font),
                                      'cv_min_font_size' => $this->get_option('cv_min_font_size', CP_CFEMAIL_DEFAULT_cv_min_font_size),
                                      'cv_max_font_size' => $this->get_option('cv_max_font_size', CP_CFEMAIL_DEFAULT_cv_max_font_size),
                                      'cv_noise' => $this->get_option('cv_noise', CP_CFEMAIL_DEFAULT_cv_noise),
                                      'cv_noise_length' => $this->get_option('cv_noise_length', CP_CFEMAIL_DEFAULT_cv_noise_length),
                                      'cv_background' => $this->get_option('cv_background', CP_CFEMAIL_DEFAULT_cv_background),
                                      'cv_border' => $this->get_option('cv_border', CP_CFEMAIL_DEFAULT_cv_border),
                                      'cv_text_enter_valid_captcha' => $this->get_option('cv_text_enter_valid_captcha', CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha)
                                     )
                      );
        }
    }


    public function plugins_loaded() {
        load_plugin_textdomain( 'contact-form-to-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    /* Filter for placing the maps into the contents */
    public function filter_content($atts) {
        global $wpdb;
        extract( shortcode_atts( array(
    		                           'id' => '',
                                       'prefill' => '',
    	                        ), $atts ) );
        if ($id != '')
            $this->item = intval($id);
        ob_start();
        $this->insert_public_item($prefill);
        $buffered_contents = ob_get_contents();
        ob_end_clean();
        return $buffered_contents;
    }


    function insert_public_item($prefill = '') {
        global $wpdb;

        $page_label = $this->get_option('vs_text_page', 'Page');
        $page_label = ($page_label==''?'Page':$page_label);
        $of_label = $this->get_option('vs_text_of', 'of');
        $of_label = ($of_label==''?'of':$of_label);
        $previous_label = $this->get_option('vs_text_previousbtn', 'Previous');
        $previous_label = ($previous_label==''?'Previous':$previous_label);
        $next_label = $this->get_option('vs_text_nextbtn', 'Next');
        $next_label = ($next_label==''?'Next':$next_label);

        if (CP_CFEMAIL_DEFER_SCRIPTS_LOADING)
        {
            wp_enqueue_style('cfte-stylepublic',  plugins_url('css/stylepublic.css', __FILE__) );
            wp_enqueue_style('cfte-stylecalendar', plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__));
            wp_enqueue_style('cfte-custompublicstyle', $this->get_site_url( false ).'?cp_cfte_resources=css');
            
            wp_deregister_script($this->prefix.'_validate_script');
            wp_register_script($this->prefix.'_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));

            wp_enqueue_script( $this->prefix.'_builder_script',
               plugins_url('/js/fbuilderf.jquery.js?nc=3', __FILE__),array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-dialog","jquery-ui-position","jquery-ui-tooltip",$this->prefix."_validate_script"), false, true );

            wp_enqueue_script( $this->prefix.'_customjs', $this->get_site_url( false ).'?cp_cfte_resources=customjs',array($this->prefix.'_builder_script'));

            wp_localize_script($this->prefix.'_builder_script', $this->prefix.'_fbuilder_config'.('_'.$this->print_counter), array('obj' =>
            '{"pub":true,"identifier":"'.('_'.$this->print_counter).'","messages": {
"required": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required),'contact-form-to-email')).'",
"email": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email),'contact-form-to-email')).'",
"datemmddyyyy": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy),'contact-form-to-email')).'",
"dateddmmyyyy": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy),'contact-form-to-email')).'",
"number": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number),'contact-form-to-email')).'",
"digits": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits),'contact-form-to-email')).'",
"max": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max),'contact-form-to-email')).'",
"min": "'.str_replace(array('"'),array('\\"'),__($this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min),'contact-form-to-email')).'",
"previous": "'.str_replace(array('"'),array('\\"'),$previous_label).'",
"next": "'.str_replace(array('"'),array('\\"'),$next_label).'",
"page": "'.str_replace(array('"'),array('\\"'),$page_label).'",
"of": "'.str_replace(array('"'),array('\\"'),$of_label).'"
}}'
            ));
        }
        else
        {
            wp_enqueue_script( "jquery" );
            wp_enqueue_script( "jquery-ui-core" );
            wp_enqueue_script( "jquery-ui-datepicker" );
            wp_enqueue_script( "jquery-ui-dialog" );
        }
        ?>
        <script type="text/javascript">
         function <?php echo esc_js($this->prefix); ?>_pform_doValidate<?php echo '_'.esc_js($this->print_counter); ?>(form)
         {
            document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.cp_ref_page.value = document.location;
            $dexQuery = jQuery.noConflict();<?php if (!is_admin() && $this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha) != 'false') { ?>
            if (document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.hdcaptcha_<?php echo esc_js($this->prefix); ?>_post.value == '') { setTimeout( "<?php echo esc_js($this->prefix); ?>_cerror<?php echo '_'.esc_js($this->print_counter); ?>()", 100); return false; }
            var result = $dexQuery.ajax({ type: "GET", url: "<?php echo $this->get_site_url_slash(); ?>ps=<?php echo '_'.esc_js($this->print_counter); ?>&<?php echo esc_js($this->prefix); ?>_pform_process=2&<?php echo esc_js($this->prefix); ?>_id=<?php echo $this->item; ?>&inAdmin=1&ps=<?php echo '_'.esc_js($this->print_counter); ?>&hdcaptcha_<?php echo esc_js($this->prefix); ?>_post="+document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.hdcaptcha_<?php echo esc_js($this->prefix); ?>_post.value, async: false }).responseText;
            if (result.indexOf("captchafailed") != -1) {
                $dexQuery("#captchaimg<?php echo '_'.esc_js($this->print_counter); ?>").attr('src', $dexQuery("#captchaimg<?php echo '_'.esc_js($this->print_counter); ?>").attr('src')+'&'+Math.floor((Math.random() * 99999) + 1));
                setTimeout( "<?php echo esc_js($this->prefix); ?>_cerror<?php echo '_'.esc_js($this->print_counter); ?>()", 100);
                return false;
            } else <?php } ?>
            {
                var cpefb_error = 0;
                $dexQuery("#<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>").find(".cpefb_error").each(function(index){
                    if ($dexQuery(this).css("display")!="none")
                        cpefb_error++;
                    });
                if (cpefb_error) return false;
                if (document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.<?php echo esc_js($this->prefix); ?>_pform_status.value != '0')
                           return false;
                document.getElementById("refpage<?php echo '_'.esc_js($this->print_counter); ?>").value = document.location;
                cfte_blink(".pbSubmit");
                document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.<?php echo esc_js($this->prefix); ?>_pform_status.value = '2';<?php
                 $option = $this->get_option('onsubmitaction', '3');
                 $message = str_replace("\n","\\n",str_replace("\r","",str_replace("'","\'", __($this->get_option('fp_return_message', 'Your message has been successfully sent.')) )));
                 if ($option == '1' || $option == '2' || $option == '3')
                 {
                    ?>document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.<?php echo esc_js($this->prefix); ?>_pform_status.value = '1';
                       $dexQuery.ajax({
                         type: "POST",
                         url: '<?php $this->get_site_url(); ?>',
                         data: $dexQuery("#<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>").serialize(),
                         success: function(data)
                         {
                             document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.<?php echo esc_js($this->prefix); ?>_pform_status.value = '0';
                             <?php
                             if ($option == '3') {
                               ?>
                                 document.getElementById('<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>').innerHTML ='<a id="cftejump"></a><?php echo esc_js($message); ?>';
                                 var tag = $dexQuery("#cftejump");
                                 $dexQuery('html,body').animate({scrollTop: tag.offset().top-60},'fast');
                               <?php
                             }
                             else if ($option == '1') {
                               ?>
                                 alert('<?php echo esc_js($message); ?>');
                                 document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.reset();
                               <?php
                             }
                             else if ($option == '2') {
                               ?>
                                 $dexQuery("#cftedialog").dialog({
                                   buttons: {
                                     Ok: function() {
                                       $dexQuery( this ).dialog( "close" );
                                       document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.reset();
                                     }
                                   }
                                 });
                                 $dexQuery(".ui-dialog-titlebar").hide();
                               <?php
                             }
                             ?>
                         }
                       });
                       return false;
                    <?php
                 }
                 else
                     echo 'return true;';
                ?>
            }
         }
         function cfte_blink(selector){
                 try {
                     $dexQuery = jQuery.noConflict();
                     $dexQuery(selector).fadeOut(700, function(){
                         $dexQuery(this).fadeIn(700, function(){
                             try {
                                 if (document.<?php echo esc_js($this->prefix); ?>_pform<?php echo '_'.esc_js($this->print_counter); ?>.<?php echo esc_js($this->prefix); ?>_pform_status.value != '0')
                                     cfte_blink(this);
                             } catch (e) {}
                         });
                     });
                 } catch (e) {}
         }
         function <?php echo esc_js($this->prefix); ?>_cerror<?php echo '_'.esc_js($this->print_counter); ?>(){$dexQuery = jQuery.noConflict();$dexQuery("#hdcaptcha_error<?php echo '_'.esc_js($this->print_counter); ?>").css('top',$dexQuery("#hdcaptcha_<?php echo esc_js($this->prefix); ?>_post<?php echo '_'.esc_js($this->print_counter); ?>").outerHeight());$dexQuery("#hdcaptcha_error<?php echo '_'.esc_js($this->print_counter); ?>").css("display","inline");}
        </script>
        <div id="cftedialog" style="display:none;"><?php echo esc_html(__($this->get_option('fp_return_message', 'Thank you.'))); ?></div>
        <?php

        $button_label = $this->get_option('vs_text_submitbtn', 'Submit');
        $button_label = ($button_label==''?'Submit':$button_label);

        // START:: code to load form settings
        $raw_form_str = str_replace("\r"," ",str_replace("\n"," ",$this->cleanJSON($this->translate_json($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)))));
        
        $form_data = json_decode( $raw_form_str );
        if( is_null( $form_data ) ){
        	$json = new JSON;
        	$form_data = $json->unserialize( $raw_form_str );
        }
        
        $preload_params = array();
        if ($prefill)
        {
            $prefill = intval($prefill);
            $event_query = "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE id=".intval($prefill);
            $event = $wpdb->get_results( $event_query );
            if (count($event))
                $preload_params = unserialize($event[0]->posted_data);
            $preload_params["itemnumber"] = $prefill;
        }        
        
        if( !is_null( $form_data ) )	
        {
        	if( !empty( $form_data[ 0 ] ) )
        	{
        		foreach( $form_data[ 0 ] as $key => $object )
        		{
        			if( isset( $object->isDataSource ) && $object->isDataSource && function_exists( 'mcrypt_encrypt' ) )
        			{
        				$connection = new stdClass();
        				$connection->connection = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $this->get_option('form_structure', CP_CONTACTFORMPP_DEFAULT_form_structure,$id), serialize( $object->list->database->databaseData ), MCRYPT_MODE_ECB ) );
        				$connection->form = $id;
        				
        				$object->list->database->databaseData = $connection;
        				$form_data[ 0 ][ $key ] = $object;
        				$raw_form_str = json_encode( $form_data );
        			} 
        			else if ($object->ftype == 'fcheck' || $object->ftype == 'fradio' || $object->ftype == 'fdropdown')
        			{
        			    for($ki=0; $ki<count($object->choicesVal); $ki++)
        			        $object->choicesVal[$ki] = $object->choicesVal[$ki];
        			    $form_data[ 0 ][ $key ] = $object;
        				$raw_form_str = json_encode( $form_data );   
        			}
                    if (count($preload_params))
                    {
                        if ($object->ftype == 'fdate')
                            $object->defaultDate = $preload_params[$object->name];
                        else if ($object->ftype == 'fradio' || $object->ftype == 'fdropdown')
                        {
                            for($ki=0; $ki<count($object->choicesVal); $ki++)
                                if ($object->choices[$ki] == $preload_params[$object->name])
                                    $object->choiceSelected = $object->choices[$ki];
                        }
                        else if ($object->ftype == 'fcheck' && is_array($preload_params[$object->name]))
                        {
                            for($ki=0; $ki<count($object->choices); $ki++)
                                if (in_array($object->choices[$ki], $preload_params[$object->name]))
        			                $object->choiceSelected[$ki] = true;
                        }
                        else if ($object->ftype == 'fPhone')
                            $object->predefined = str_replace("-", " ", $preload_params[$object->name]);
                        else if (property_exists($object, 'predefined'))
                            $object->predefined = $preload_params[$object->name];
                        
                        if (property_exists($object, 'predefinedClick')) // remove placeholders 
                            $object->predefinedClick = 0;
                            
                            
                        $form_data[ 0 ][ $key ] = $object;
                        $raw_form_str = json_encode( $form_data );
                    }
        		}
        	}
        	
        }
        
        $raw_form_str = str_replace('"','&quot;',esc_attr($raw_form_str));
        // END:: code to load form settings
        
        
        if (!defined('CP_AUTH_INCLUDE')) define('CP_AUTH_INCLUDE',true);
        @include __DIR__ . '/cp-public-int.inc.php';
        if (!CP_CFEMAIL_DEFER_SCRIPTS_LOADING)
        {
            $prefix_ui = '';
            if (@file_exists(__DIR__.'/../../../wp-includes/js/jquery/ui/jquery.ui.core.min.js'))
                $prefix_ui = 'jquery.ui.';
            // This code won't be used in most cases. This code is for preventing problems in wrong WP themes and conflicts with third party plugins.
            // It can be manually activated by the user if needed from the troubleshoot settings area of the plugin
            ?>
                 <?php $plugin_url = plugins_url('', __FILE__); ?>
                 <link href="<?php echo plugins_url('css/stylepublic.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
                 <link href="<?php echo plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/jquery.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'core.min.js'; ?>'></script>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'datepicker.min.js'; ?>'></script>
<?php if (@file_exists(__DIR__.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'widget.min.js')) { ?><script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'widget.min.js'; ?>'></script><?php } ?>
<?php if (@file_exists(__DIR__.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'position.min.js')) { ?><script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'position.min.js'; ?>'></script><?php } ?>
                 <script type='text/javascript' src='<?php echo $plugin_url.'/../../../wp-includes/js/jquery/ui/'.$prefix_ui.'tooltip.min.js'; ?>'></script>                 
                 <script type='text/javascript' src='<?php echo plugins_url('js/jquery.validate.js', __FILE__); ?>'></script>
                 <script type='text/javascript'>
                 /* <![CDATA[ */
                 var <?php echo esc_js($this->prefix); ?>_fbuilder_config<?php echo '_'.esc_js($this->print_counter); ?> = {"obj":"{\"pub\":true,\"identifier\":\"<?php echo '_'.esc_js($this->print_counter); ?>\",\"messages\": {\n    \t                \t\"required\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required));?>\",\n    \t                \t\"email\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email));?>\",\n    \t                \t\"datemmddyyyy\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy));?>\",\n    \t                \t\"dateddmmyyyy\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy));?>\",\n    \t                \t\"number\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number));?>\",\n    \t                \t\"digits\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits));?>\",\n    \t                \t\"max\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max));?>\",\n    \t                \t\"min\": \"<?php echo str_replace(array('"'),array('\\"'),$this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min));?>\",\"previous\": \"<?php echo str_replace(array('"'),array('\\"'),$previous_label); ?>\",\"next\": \"<?php echo str_replace(array('"'),array('\\"'),$next_label); ?>\"\n    \t                }}"};
                 /* ]]> */
                 </script>
                 <script type='text/javascript' src='<?php echo plugins_url('js/fbuilderf.jquery.js?nc=3', __FILE__); ?>'></script>
                 <script type='text/javascript' src='<?php echo $this->get_site_url( false ).'?cp_cfte_resources=customjs'; ?>'></script>
            <?php
        }
        $this->print_counter++;
    }


    /* Code for the admin area */

    public function plugin_page_links($links) {
        $customAdjustments_link = '<a href="https://form2email.dwbooster.com/download">'.__('Upgrade','contact-form-to-email').'</a>';
    	array_unshift($links, $customAdjustments_link);
        $settings_link = '<a href="admin.php?page='.$this->menu_parameter.'">'.__('Settings','contact-form-to-email').'</a>';
    	array_unshift($links, $settings_link);
    	$help_link = '<a href="https://form2email.dwbooster.com/support">'.__('Documentation','contact-form-to-email').'</a>';
    	array_unshift($links, $help_link);
    	$s_link = '<a href="https://wordpress.org/support/plugin/contact-form-to-email#new-post">'.__('Support','contact-form-to-email').'</a>';
    	array_unshift($links, $s_link);
    	return $links;
    }


    public function admin_menu() {
        add_options_page($this->plugin_name.' Options', $this->plugin_name, 'manage_options', $this->menu_parameter, array($this, 'settings_page') );
        add_menu_page( $this->plugin_name.' Options', $this->plugin_name, 'edit_pages', $this->menu_parameter, array($this, 'settings_page') );
        if (class_exists('CP_REFTRACK_Plugin'))
            add_submenu_page( $this->menu_parameter, 'Referral report', 'Referral report', 'read', $this->menu_parameter."_reftracking", array($this, 'settings_page') );
        else
            add_submenu_page( $this->menu_parameter, 'Add Referral tracking', 'Add Referral tracking', 'read', $this->menu_parameter."_reftracking", array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Help: Online demo', 'Help: Online demo', 'read', $this->menu_parameter."_demo", array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Help: Documentation', 'Help: Documentation', 'read', $this->menu_parameter."_docs", array($this, 'settings_page') );
        add_submenu_page( $this->menu_parameter, 'Help: Free support', 'Help: Free support', 'read', $this->menu_parameter."_fsupport", array($this, 'settings_page') );

        add_submenu_page( $this->menu_parameter, 'Upgrade', 'Upgrade', 'edit_pages', $this->menu_parameter."_upgrade", array($this, 'settings_page') );
    }


    function insert_button() {
        print '<a href="javascript:send_to_editor(\'[CONTACT_FORM_TO_EMAIL]\');" title="'.__('Insert','contact-form-to-email').' '.$this->plugin_name.'"><img hspace="5" src="'.plugins_url('/images/cp_form.gif', __FILE__).'" alt="'.__('Insert','contact-form-to-email').' '.$this->plugin_name.'" /></a>';
    }


    public function settings_page() {
        global $wpdb;
        if ($this->get_param("cal") || $this->get_param("cal") == '0' || $this->get_param("pwizard") == '1')
        {
            $this->item = intval($this->get_param("cal"));
            if (isset($_GET["edit"]) && $_GET["edit"] == '1')
                @include_once __DIR__ . '/cp_admin_int_edition.inc.php';
            else if ($this->get_param("list") == '1')
                @include_once __DIR__ . '/cp-admin-int-message-list.inc.php';
            else if ($this->get_param("edititem"))
                @include_once __DIR__ . '/cp-admin-int-edit-booking.inc.php';            
            else if ($this->get_param("addbk") == '1')
                @include_once __DIR__ . '/cp-admin-int-add-booking.inc.php';
            else if ($this->get_param("report") == '1')
                @include_once __DIR__ . '/cp-admin-int-report.inc.php';
            else if ($this->get_param("pwizard") == '1')
            {
                if ($this->get_param("cal"))
                    $this->item = intval($this->get_param("cal"));
                @include_once __DIR__ . '/cp-publish-wizzard.inc.php';
            }
            else
                @include_once __DIR__ . '/cp-admin-int.inc.php';
        }
        else if ($this->get_param("page") == $this->menu_parameter.'_reftracking')
        {
            if (class_exists('CP_REFTRACK_Plugin'))
                echo("Redirecting to referral report...<script type='text/javascript'>document.location='?page=cp_reftrack';</script>");
            else    
                echo("Redirecting to referral add-on page...<script type='text/javascript'>document.location='https://wordpress.org/plugins/cp-referrer-and-conversions-tracking/';</script>");
            exit;
        }        
        else if ($this->get_param("page") == $this->menu_parameter.'_upgrade')
        {
            echo("Redirecting to upgrade page...<script type='text/javascript'>document.location='https://form2email.dwbooster.com/download';</script>");
            exit;
        }
        else if ($this->get_param("page") == $this->menu_parameter.'_demo')
        {
            echo("Redirecting to demo page...<script type='text/javascript'>document.location='https://form2email.dwbooster.com/home#demos';</script>");
            exit;
        }
        else if ($this->get_param("page") == $this->menu_parameter.'_docs')
        {
            echo("Redirecting to demo page...<script type='text/javascript'>document.location='https://form2email.dwbooster.com/documentation?open=1';</script>");
            exit;
        }
        else if ($this->get_param("page") == $this->menu_parameter.'_fsupport')
        {
            echo("Redirecting to demo page...<script type='text/javascript'>document.location='https://wordpress.org/support/plugin/contact-form-to-email#new-post';</script>");
            exit;
        }
        else
            @include_once __DIR__ . '/cp-admin-int-list.inc.php';
    }

    
    function gutenberg_block() {
        global $wpdb;

        wp_enqueue_script( 'cfte_gutenberg_editor', plugins_url('/js/block.js', __FILE__));

        wp_enqueue_style('cfte-publicstyle', plugins_url('css/stylepublic.css', __FILE__));        

        wp_deregister_script($this->prefix.'_validate_script');
        wp_register_script($this->prefix.'_validate_script', plugins_url('/js/jquery.validate.js', __FILE__));
        wp_enqueue_script( $this->prefix.'_builder_script',
               plugins_url('/js/fbuilderf.jquery.js?nc=3', __FILE__),array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip",$this->prefix."_validate_script"), false, true );

        $forms = array();
        $rows = $wpdb->get_results("SELECT id,form_name FROM ".$wpdb->prefix.$this->table_items." ORDER BY form_name");
        foreach ($rows as $item)
           $forms[] = array (
                            'value' => $item->id,
                            'label' => $item->form_name,
                            );

        wp_localize_script( 'cfte_gutenberg_editor', 'cfte_forms', array(
                            'forms' => $forms,
                            'siteUrl' => get_site_url()
                          ) );
    }


    public function render_form_admin ($atts) {
        $is_gutemberg_editor = defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];
        if (!$is_gutemberg_editor)
            return $this->filter_content (array('id' => $atts["formId"]));
        else if ($atts["formId"])
        {
            $this->setId($atts["formId"]);
            return '<input type="hidden" name="form_structure'.$atts["instanceId"].'" id="form_structure'.$atts["instanceId"].'" value="'.esc_attr($this->get_option('form_structure')).'" /><fieldset class="ahbgutenberg_editor" disabled><div id="fbuilder"><div id="fbuilder_'.$atts["instanceId"].'"><div id="formheader_'.$atts["instanceId"].'"></div><div id="fieldlist_'.$atts["instanceId"].'"></div></div></div></fieldset>';
        }
        else
            return __('Contact form inserted. <b>Save and reload this page</b> to render the contact form.','contact-form-to-email');
    }


    function insert_adminScripts($hook) {
        if ($this->get_param("page") == $this->menu_parameter && $this->get_param("addbk") != '1' && !$this->get_param("edititem"))
        {           
            wp_enqueue_script( $this->prefix.'_builder_script', plugins_url('/js/fbuilderf.jquery.js?nc=3', __FILE__),array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-tabs","jquery-ui-droppable","jquery-ui-button","jquery-ui-datepicker") );
            wp_enqueue_style('cfte-adminstyles', plugins_url('css/style.css', __FILE__) );
            wp_enqueue_style('cfte-admincalendarstyles', plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__) );
            wp_enqueue_style('cfte-newadminstyle', plugins_url('/css/newadminlayout.css', __FILE__));
            //wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
			if ($this->get_param("report") == '1')
				wp_enqueue_script( $this->prefix.'_excanvas', plugins_url('/js/excanvas.min.js', __FILE__));
        }
        if( 'post.php' != $hook  && 'post-new.php' != $hook )
            return;
        // space to include some script in the post or page areas if needed
    }

    /* hook for checking posted data for the admin area */

    function data_management_loaded() {
        global $wpdb;

        $action = $this->get_param('cp_contactformtoemail_do_action_loaded');
    	if (!$action) return; // go out if the call isn't for this one

        if ($this->get_param('cp_contactformtoemail_id')) $this->item = $this->get_param('cp_contactformtoemail_id');

        if ($action == "wizard" && current_user_can('manage_options') && wp_verify_nonce( $_POST['anonce'], 'cpappb_actions_pwizard'))
        {
            $shortcode = '['.$this->shorttag.' id="'.$this->item .'"]';
            $this->postURL = $this->publish_on(sanitize_text_field($_POST["whereto"]), sanitize_text_field(@$_POST["publishpage"]), sanitize_text_field(@$_POST["publishpost"]), $shortcode, sanitize_text_field($_POST["posttitle"]));
            return;
        }

        // ...
        echo 'Some unexpected error happened. If you see this error contact the support service at https://form2email.dwbooster.com/contact-us';

        exit();
    }


    private function publish_on($whereto, $publishpage = '', $publishpost = '', $content = '', $posttitle = 'Booking Form')
    {
        global $wpdb;
        $id = '';
        if ($whereto == '0' || $whereto =='1') // new page
        {
            $my_post = array(
              'post_title'    => $posttitle,
              'post_type' => ($whereto == '0'?'page':'post'),
              'post_content'  => $content, // 'This is a <b>preview</b> page, remember to publish it if needed. You can edit the full form settings into the admin settings page.<br /><br /> '.
              'post_status'   => 'publish'
            );

            // Insert the post into the database
            $id = wp_insert_post( $my_post );
        }
        else
        {
            $id = ($whereto == '2'?$publishpage:$publishpost);
            $post = get_post( $id );
            $pos = strpos($post->post_content,$content);
            if ($pos === false)
            {
                $my_post = array(
                      'ID'           => $id,
                      'post_content' => $content.$post->post_content,
                  );
                // Update the post into the database
                wp_update_post( $my_post );
            }
        }
        return get_permalink($id);
    }


    function data_management() {
        global $wpdb, $cp_cfte_plugin;

    	if( isset( $_REQUEST[ 'cp_cfte_resources' ] ) )
    	{
    		if( $_REQUEST[ 'cp_cfte_resources' ] == 'css' )
    		{
                header("Content-type: text/css");
    			$custom_styles = base64_decode(get_option('CP_CFTE_CSS', ''));
                echo $custom_styles;
    		}
            else if( $_REQUEST[ 'cp_cfte_resources' ] == 'customjs' )
    		{
                header("Content-type: application/javascript");
    			$custom_scripts = base64_decode(get_option('CP_CFTE_JS', ''));
                echo $custom_scripts;
    		}
    		exit;
    	}
        
        $this->check_reports();

        if ($this->get_param($this->prefix.'_encodingfix') == '1')
        {
            $wpdb->query('alter table '.$wpdb->prefix.$this->table_items.' convert to character set utf8 collate utf8_unicode_ci;');
            $wpdb->query('alter table '.$wpdb->prefix.$this->table_messages.' convert to character set utf8 collate utf8_unicode_ci;');
            echo 'Ok, encoding fixed.';
            exit;
        }

        if ($this->get_param($this->prefix.'_captcha') == 'captcha' )
        {
            @include_once __DIR__ . '/captcha/captcha.php';
            exit;
        }


        if ($this->get_param($this->prefix.'_csv') && current_user_can('edit_pages') && is_admin() )
        {
            $this->export_csv();
            return;
        }

        if ( $this->get_param($this->prefix.'_post_options') && current_user_can('edit_pages') && is_admin() )
        {
            $this->save_options();
            return;
        }

        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['CP_CFTE_post_edition'] ) && current_user_can('edit_pages') && is_admin() )
        {
            $this->save_edition();
            return;
        }

    	if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST[$this->prefix.'_pform_process'] ) )
    	    if ( 'GET' != $_SERVER['REQUEST_METHOD'] || !isset( $_GET['hdcaptcha_'.$this->prefix.'_post'] ) )
    		    return;

        if ($this->get_param($this->prefix.'_id')) $this->item = $this->get_param($this->prefix.'_id');

        if (function_exists('session_start')) @session_start();
        if (isset($_GET["ps"])) $sequence = sanitize_key($_GET["ps"]); else if (isset($_POST["cp_pform_psequence"])) $sequence = sanitize_key($_POST["cp_pform_psequence"]);
        
        $captcha_tr = '';
        if (!empty($_COOKIE['rand_code'.$sequence])) $captcha_tr = get_transient( "cpeople-captcha-".sanitize_key($_COOKIE['rand_code'.$sequence]));

        if (
               (!is_admin() && $this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha) != 'false') &&
               ( !isset($_SESSION['rand_code'.$sequence]) || (strtolower($this->get_param('hdcaptcha_'.$this->prefix.'_post')) != strtolower($_SESSION['rand_code'.$sequence])) ||
                 ($_SESSION['rand_code'.$sequence] == '')
               )
               &&
               ( ((strtolower($this->get_param('hdcaptcha_'.$this->prefix.'_post'))) != ($captcha_tr)) ||
                 ($captcha_tr == '')
               )
           )
        {
            $_SESSION['rand_code'.$sequence] = '';
            setCookie('rand_code'.$sequence, '', time()+36000,"/");
            echo 'captchafailed';
            exit;
        }

    	// if this isn't the real post (it was the captcha verification) then echo ok and exit
        if ( 'POST' != $_SERVER['REQUEST_METHOD'] || ! isset( $_POST[$this->prefix.'_pform_process'] ) )
    	{
    	    echo 'ok';
            exit;
    	}
        
        if ($this->get_option('cv_enable_captchaintelligent', CP_CFEMAIL_DEFAULT_cv_enable_captchainvisible) == 'true')
        {
            if (
                (!isset($_POST["cftecontrolmessage"]) || $_POST["cftecontrolmessage"] != '') ||
                (!isset($_POST["cftehp"]) || $_POST["cftehp"] != '25') ||
                !wp_verify_nonce( $_POST['anonce'], 'cfte_actions_emailform')
               )
               {
                   echo 'Invisible antispam protection prevented this submission. Please contact our support service if you think this is an error. Thank you.';
                   exit;
               }
        }
        
        

        //if (get_magic_quotes_gpc())
            foreach ($_POST as $item => $value)
                $_POST[$item] = (is_array($value)?$value:stripcslashes($value));

        // get form info
        //---------------------------
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        $form_data = json_decode($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)));
        $fields = array();
        foreach ($form_data[0] as $item)
        {
            $fields[$item->name] = $item->title;
            if ($item->ftype == 'fPhone') // join fields for phone fields
            {
                for($i=0; $i<=substr_count($item->dformat," "); $i++)
                {
                    $_POST[$item->name.$sequence] .= ($_POST[$item->name.$sequence."_".$i]!=''?($i==0?'':'-').$_POST[$item->name.$sequence."_".$i]:'');
                    unset($_POST[$item->name.$sequence."_".$i]);
                }
            }
        }


        // grab posted data
        //---------------------------
        $buffer = "";
        $params = array();
        $founddata = false;
        if (!(isset($_POST["edititem"]) && $_POST["edititem"]))
            $params["referrer"] = esc_url_raw($_POST["refpage".$sequence]);
        foreach ($_POST as $item => $value)
            if (isset($fields[str_replace($sequence,'',$item)]))
            {
                $buffer .= $fields[str_replace($sequence,'',$item)] . ": ". (is_array($value)?(implode(", ",$value)):($value)) . "\n\n";
                $params[str_replace($sequence,'',$item)] = $value;
                $founddata = true;
            }

        foreach ($_FILES as $item => $value)
            if (isset($fields[str_replace($sequence,'',$item)]) && $this->check_upload($_FILES[$item]))
            {
                $buffer .= $fields[str_replace($sequence,'',$item)] . ": ". $value["name"] . "\n\n";
                $params[str_replace($sequence,'',$item)] = $value["name"];
                $founddata = true;
                $movefile = wp_handle_upload( $_FILES[$item], array( 'test_form' => false ) );
                if ( $movefile )
                {
                    $params[str_replace($sequence,'',$item)."_link"] = $movefile["file"];
                    $params[str_replace($sequence,'',$item)."_url"] = $movefile["url"];
                }
                // else {print_r($movefile);exit;}    // un-comment this line if the uploads aren't working
            }
        $buffer_A = $buffer;

        if (!$founddata)
        {
            echo 'Empty post! No data received.';
            exit;
        }
                
        $_SESSION['rand_code'.$sequence] = '';
        if (!empty($_COOKIE['rand_code'.$sequence])) $captcha_tr = delete_transient( "cpeople-captcha-".sanitize_key($_COOKIE['rand_code'.$sequence]));
        setCookie('rand_code'.$sequence, '', time()+36000,"/");

        $saveipaddr = ('true' == $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info));
        
	    /**
	     * Action called before insert the data into database. 
	     * To the function is passed an array with submitted data.
	     */							
	    do_action_ref_array( 'cfte_process_data_before_insert', array(&$params) );
        
        // insert into database
        //---------------------------
        $wpdb->query("ALTER TABLE ".$wpdb->prefix.$this->table_messages." CHANGE `ipaddr` `ipaddr` VARCHAR(250)");
        $to = $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field);
        if (isset($_POST["edititem"]) && $_POST["edititem"])
        {
            $rows_affected = $wpdb->update( $wpdb->prefix.$this->table_messages, array( 
                                                                                    'notifyto' => sanitize_email(@$_POST[$to.$sequence]?$_POST[$to.$sequence]:''),
                                                                                    'posted_data' => serialize($params),
                                                                                    'data' =>$buffer_A
                                                                                   ),
                                                                                   array (
                                                                                    'id' => intval($_POST["edititem"])
                                                                                   )
                                                                                   );
        }
        else        
            $rows_affected = $wpdb->insert( $wpdb->prefix.$this->table_messages, array( 'formid' => $this->item,
                                                                                    'time' => current_time('mysql'),
                                                                                    'ipaddr' => ($saveipaddr?$this->getRealUserIP():'-'),
                                                                                    'notifyto' => sanitize_email(@$_POST[$to.$sequence]?$_POST[$to.$sequence]:''),
                                                                                    'posted_data' => serialize($params),
                                                                                    'data' =>$buffer_A
                                                                                   ) );
        if (!$rows_affected && !isset($_POST["edititem"]))
        {
            echo 'Error saving data! Please try again.';
            echo '<br /><br />If the error persists  please be sure you are using the latest version and in that case contact support service at https://form2email.dwbooster.com/contact-us?debug=db';
            exit;
        }

        if (isset($_POST["edititem"]) && $_POST["edititem"])
            $item_number = intval($_POST["edititem"]);
        else
        {
            $myrows = $wpdb->get_results( "SELECT MAX(id) as max_id FROM ".$wpdb->prefix.$this->table_messages );
            $item_number = $myrows[0]->max_id;
        }


	    // Call action for data processing
	    //---------------------------------
	    $params[ 'itemnumber' ] = $item_number;
	    
	    /**
	     * Action called after inserted the data into database. 
	     * To the function is passed an array with submitted data.
	     */							
	    do_action( 'cfte_process_data', $params );

        $this->ready_to_go_reservation($item_number, "", $params);

        if (is_admin())
            return;  
         
        if ($_POST[ $this->prefix."_pform_status"] == '1')
            echo 'OK';
        else
            header("Location: ".$this->get_option('fp_return_page', CP_CFEMAIL_DEFAULT_fp_return_page));
        exit();
    }


    function check_upload($uploadfiles) {
        $filename = $uploadfiles['name'];
        $filetype = wp_check_filetype( basename( $filename ), null );

        if ( in_array ($filetype["ext"],array("php","asp","aspx","cgi","pl","perl","exe","cmd","js","msi")) )
            return false;
        else
            return true;
    }


    function ready_to_go_reservation($itemnumber, $payer_email = "", $params = array())
    {

        global $wpdb;

        $myrows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE id=%d", $itemnumber ) );

        $mycalendarrows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM '.$wpdb->prefix.$this->table_items.' WHERE `id`=%d', $myrows[0]->formid ) );

        $this->item = $myrows[0]->formid;

        $buffer_A = $myrows[0]->data;
        $buffer = $buffer_A;

        if ('true' == $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info))
        {
            $buffer .="ADDITIONAL INFORMATION\n"
                  ."*********************************\n"
                  ."IP: ".$myrows[0]->ipaddr."\n"
                  ."Server Time:  ".date("Y-m-d H:i:s")."\n";
        }

        // 1- Send email
        //---------------------------
        $attachments = array();
        if ('html' == $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format))
            $message = str_replace('<'.'%INFO%'.'>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer)),$this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message));
        else
            $message = str_replace('<'.'%INFO%'.'>',$buffer,$this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message));
        $subject = $this->get_option('fp_subject', CP_CFEMAIL_DEFAULT_fp_subject);
        foreach ($params as $item => $value)
        {
            $message = str_replace('<'.'%'.$item.'%'.'>',(is_array($value)?(implode(", ",$value)):($value)),$message);
            $subject = str_replace('<'.'%'.$item.'%'.'>',(is_array($value)?(implode(", ",$value)):($value)),$subject);
            if (strpos($item,"_link"))
                $attachments[] = $value;
        }

        $message = str_replace('<'.'%itemnumber%'.'>',$itemnumber,$message);
        $subject = str_replace('<'.'%itemnumber%'.'>',$itemnumber,$subject);

        for ($i=0;$i<500;$i++)
        {
            $subject = str_replace('<'.'%fieldname'.$i.'%'.'>',"",$subject);
            $message = str_replace('<'.'%fieldname'.$i.'%'.'>',"",$message);
        }

        $from = trim($this->get_option('fp_from_email', (defined('CP_CFEMAIL_DEFAULT_fp_from_email') ? CP_CFEMAIL_DEFAULT_fp_from_email : '') ));
        $to = explode(",",$this->get_option('fp_destination_emails', (defined('CP_CFEMAIL_DEFAULT_fp_destination_emails') ? CP_CFEMAIL_DEFAULT_fp_destination_emails : '') ));
        if ('html' == $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";

        $replyto = $myrows[0]->notifyto;
        if ($this->get_option('fp_emailfrommethod', "fixed") == "customer")
            $from_1 = $replyto;
        else
            $from_1 = $from;

        // if is_admin and not required emails end function here
        if (is_admin() && !isset($_POST["sendemails_admin"]))   
            return;     
        
        if ($this->get_option('fp_enableemail', 'true') != 'false')
        {
            foreach ($to as $item)
                if (trim($item) != '')
                {
                    if (!strpos($from_1,">"))
                        $from_1 = '"'.$from_1.'" <'.$from_1.'>';
                    wp_mail(trim($item), $subject, $message,
                        "From: ".$from_1."\r\n".
                        ($replyto!=''?"Reply-To: ".$replyto."\r\n":'').
                        $content_type.
                        "X-Mailer: PHP/" . phpversion(), $attachments);
                }
        }

        // 2- Send copy to user
        //---------------------------
        $to = $this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field);
        $_POST[$to] = $myrows[0]->notifyto;
        if ((trim($_POST[$to]) != '' || $payer_email != '') && 'true' == $this->get_option('cu_enable_copy_to_user', CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user))
        {
            if ('html' == $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format))
                $message = str_replace('<'.'%INFO%'.'>',str_replace("\n","<br />",str_replace('<','&lt;',$buffer_A)).'</pre>',$this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message));
            else
                $message = str_replace('<'.'%INFO%'.'>',$buffer_A,$this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message));
            $subject = $this->get_option('cu_subject', CP_CFEMAIL_DEFAULT_cu_subject);
            foreach ($params as $item => $value)
            {
                $message = str_replace('<'.'%'.$item.'%'.'>',(is_array($value)?(implode(", ",$value)):($value)),$message);
                $subject = str_replace('<'.'%'.$item.'%'.'>',(is_array($value)?(implode(", ",$value)):($value)),$subject);
            }
            if ('html' == $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format)) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";

            for ($i=0;$i<500;$i++)
            {
                $subject = str_replace('<'.'%fieldname'.$i.'%'.'>',"",$subject);
                $message = str_replace('<'.'%fieldname'.$i.'%'.'>',"",$message);
            }

            if (!strpos($from,">"))
                $from = '"'.$from.'" <'.$from.'>';

            if ($_POST[$to] != '')
                wp_mail(trim($_POST[$to]), $subject, $message,
                        "From: ".$from."\r\n".
                        $content_type.
                        "X-Mailer: PHP/" . phpversion());
            if ($_POST[$to] != $payer_email && $payer_email != '')
                wp_mail(trim($payer_email), $subject, $message,
                        "From: ".$from."\r\n".
                        $content_type.
                        "X-Mailer: PHP/" . phpversion());
        }

    }


    function save_edition()
    {
        $verify_nonce = wp_verify_nonce( $_POST['rsave'], 'cfte_update_actions_custom');
        if (!$verify_nonce)
        {
            echo 'Error: Form cannot be authenticated. Please contact our <a href="https://form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
            return;
        }

        foreach ($_POST as $item => $value)
            if (!is_array($value))
                $_POST[$item] = stripcslashes($value);
        if (substr_count($_POST['editionarea'],"\\\""))
            $_POST["editionarea"] = stripcslashes($_POST["editionarea"]);
        if ($_POST["cfwpp_edit"] == 'js')
            update_option('CP_CFTE_JS', base64_encode($_POST["editionarea"]));
        else if ($_POST["cfwpp_edit"] == 'css')
            update_option('CP_CFTE_CSS', base64_encode($_POST["editionarea"]));
    }


    function save_options()
    {
        global $wpdb;

        $verify_nonce = wp_verify_nonce( $_POST['rsave'], 'cfpoll_update_actions_post');
        if (!$verify_nonce)
        {
            echo 'Error: Form cannot be authenticated. Please contact our <a href="https://form2email.dwbooster.com/contact-us">support service</a> for verification and solution. Thank you.';
            return;
        }

        $this->item = intval($_POST[$this->prefix."_id"]);

        if ((substr_count($_POST['form_structure_control'],"\\") > 1) || substr_count($_POST['form_structure_control'],"\\\"title\\\":"))
            foreach ($_POST as $item => $value)
                if (!is_array($value))
                    $_POST[$item] = stripcslashes($value);
        
        
        if (false == get_option('CFTE_ONE_TIME_2UPDATE',false)) 
        {            
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_enable", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_days", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_hour", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_emails", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_subject", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_emailformat", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "rep_message", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items,'vs_text_page',"text");
            $this->add_field_verify($wpdb->prefix.$this->table_items,'vs_text_of',"text");
            $this->add_field_verify($wpdb->prefix.$this->table_items,'vs_text_submitbtn',"text");
            $this->add_field_verify($wpdb->prefix.$this->table_items,'vs_text_previousbtn',"text");
            $this->add_field_verify($wpdb->prefix.$this->table_items,'vs_text_nextbtn',"text");
            
            $this->add_field_verify($wpdb->prefix.$this->table_items, "onsubmitaction", "text");
            $this->add_field_verify($wpdb->prefix.$this->table_items, "fp_return_message", "text");        
            $this->add_field_verify($wpdb->prefix.$this->table_items, "fp_enableemail", "text");
            
            $this->add_field_verify($wpdb->prefix.$this->table_items, "fp_emailfrommethod", "text");
            
            $this->add_field_verify($wpdb->prefix.$this->table_items, "cv_enable_captchaintelligent", "text");
            update_option('CFTE_ONE_TIME_2UPDATE',true);
        }        
        


        $wpdb->update ( $wpdb->prefix.$this->table_items,
                        array(
                              'form_structure' => $this->clean_sanitize($_POST['form_structure']),

                              'fp_from_email' => sanitize_text_field($_POST['fp_from_email']),
                              'fp_destination_emails' => sanitize_text_field($_POST['fp_destination_emails']),
                              'fp_subject' => $this->clean_sanitize($_POST['fp_subject']),
                              'fp_inc_additional_info' => sanitize_text_field($_POST['fp_inc_additional_info']),
                              'fp_return_page' => sanitize_text_field($_POST['fp_return_page']),
                              'fp_message' => $this->clean_sanitize($_POST['fp_message']),
                              'fp_emailformat' => sanitize_text_field($_POST['fp_emailformat']),

                              'cu_enable_copy_to_user' => sanitize_text_field($_POST['cu_enable_copy_to_user']),
                              'cu_user_email_field' => sanitize_text_field(@$_POST['cu_user_email_field']),
                              'cu_subject' => $this->clean_sanitize($_POST['cu_subject']),
                              'cu_message' => $this->clean_sanitize($_POST['cu_message']),
                              'cu_emailformat' => sanitize_text_field($_POST['cu_emailformat']),
                              'fp_emailfrommethod' => sanitize_text_field($_POST['fp_emailfrommethod']),

                              'onsubmitaction' => sanitize_text_field($_POST['onsubmitaction']),
                              'fp_return_message' => sanitize_text_field($_POST['fp_return_message']),
                              'fp_enableemail' => sanitize_text_field($_POST['fp_enableemail']),

                              'vs_text_is_required' => sanitize_text_field($_POST['vs_text_is_required']),
                              'vs_text_is_email' => sanitize_text_field($_POST['vs_text_is_email']),
                              'vs_text_datemmddyyyy' => sanitize_text_field($_POST['vs_text_datemmddyyyy']),
                              'vs_text_dateddmmyyyy' => sanitize_text_field($_POST['vs_text_dateddmmyyyy']),
                              'vs_text_number' => sanitize_text_field($_POST['vs_text_number']),
                              'vs_text_digits' => sanitize_text_field($_POST['vs_text_digits']),
                              'vs_text_max' => sanitize_text_field($_POST['vs_text_max']),
                              'vs_text_min' => sanitize_text_field($_POST['vs_text_min']),
                              'vs_text_page' => sanitize_text_field($_POST['vs_text_page']),
                              'vs_text_of' => sanitize_text_field($_POST['vs_text_of']),
                              'vs_text_submitbtn' => sanitize_text_field($_POST['vs_text_submitbtn']),
                              'vs_text_previousbtn' => sanitize_text_field($_POST['vs_text_previousbtn']),
                              'vs_text_nextbtn' => sanitize_text_field($_POST['vs_text_nextbtn']),

                              'rep_enable' => sanitize_text_field($_POST['rep_enable']),
                              'rep_days' => sanitize_text_field($_POST['rep_days']),
                              'rep_hour' => sanitize_text_field($_POST['rep_hour']),
                              'rep_emails' => sanitize_text_field($_POST['rep_emails']),
                              'rep_subject' => $this->clean_sanitize($_POST['rep_subject']),
                              'rep_emailformat' => sanitize_text_field($_POST['rep_emailformat']),
                              'rep_message' => $this->clean_sanitize($_POST['rep_message']),

                              'cv_enable_captchaintelligent' => $_POST['cv_enable_captchaintelligent'],
                              'cv_enable_captcha' => sanitize_text_field($_POST['cv_enable_captcha']),
                              'cv_width' => sanitize_text_field($_POST['cv_width']),
                              'cv_height' => sanitize_text_field($_POST['cv_height']),
                              'cv_chars' => sanitize_text_field($_POST['cv_chars']),
                              'cv_font' => sanitize_text_field($_POST['cv_font']),
                              'cv_min_font_size' => sanitize_text_field($_POST['cv_min_font_size']),
                              'cv_max_font_size' => sanitize_text_field($_POST['cv_max_font_size']),
                              'cv_noise' => sanitize_text_field($_POST['cv_noise']),
                              'cv_noise_length' => sanitize_text_field($_POST['cv_noise_length']),
                              'cv_background' => sanitize_text_field(str_replace('#','',$_POST['cv_background'])),
                              'cv_border' => sanitize_text_field(str_replace('#','',$_POST['cv_border'])),
                              'cv_text_enter_valid_captcha' => sanitize_text_field($_POST['cv_text_enter_valid_captcha'])
    	                     )
                        , array( 'id' => $this->item ));
                        
        if (isset($_POST["savepublish"]))
        {
            echo '<script type="text/javascript">document.location="?page='.$this->menu_parameter.'&pwizard=1&cal='.$this->item.'";</script>';
        } else if (isset($_POST["savereturn"]))
        {
            echo '<script type="text/javascript">document.location="?page='.$this->menu_parameter.'&confirm=1";</script>';
        }                        
    }


    function get_form_field_label ($fieldid, $form)
    {
            foreach($form as $item)
                if ($item->name == $fieldid)
                {
                    if (isset($item->shortlabel) && $item->shortlabel != '')
                        return $item->shortlabel;
                    else
                        return $item->title;
                }
        return $fieldid;
    }


    function clean_csv_value($value)
    {
        $value = trim($value);
        while (strlen($value) > 1 && in_array($value[0],array('=','@')))
            $value = trim(substr($value, 1));
        return $value;
    }
    

    function export_csv ()
    {
        if (!is_admin())
            return;
        global $wpdb;

        $this->item = intval($this->get_param("cal"));

        if ($this->item > 0)
        {
            $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)));
            $form = $form[0];
        }
        else
            $form = array();

        $cond = '';
        if ($this->get_param("search")) $cond .= " AND (data like '%".esc_sql($this->get_param("search"))."%' OR posted_data LIKE '%".esc_sql($this->get_param("search"))."%')";
        if ($this->get_param("dfrom")) $cond .= " AND (`time` >= '".esc_sql( $this->get_param("dfrom") . ($this->get_param("tfrom")?' '.$this->get_param("tfrom"):'') )."')";
        if ($this->get_param("dto")) $cond .= " AND (`time` <= '".esc_sql($this->get_param("dto") . (@$this->get_param("tto")?' '.$this->get_param("tto"):' 23:59:59') )."')";
        if ($this->item > 0) $cond .= " AND formid=".intval($this->item);

        $events = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE 1=1 ".$cond." ORDER BY `time` DESC" );

        if ($this->include_user_data_csv)
            $fields = array("ID","Form ID",  "IP Address", "email");
        else
            $fields = array("ID","Form", "Time", "email");
        $values = array();
        foreach ($events as $item)
        {
            if ($this->include_user_data_csv)
                $value = array($item->id,$item->formid, $item->time, $item->ipaddr, $item->notifyto);
            else
                $value = array($item->id,$this->get_option('form_name',''), $item->time, $item->notifyto);
            if ($item->posted_data)
                $data = unserialize($item->posted_data);
            else
                $data = array();

            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            if (is_array($data)) foreach ($data as $k => $d)
            {
               $fields[] = $k;
               $value[] = $d;
            }
            $values[] = $value;
        }

        $filename = sanitize_file_name($this->get_option('form_name','export')).'_'.date("m_d_y");

        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=".$filename.".csv");

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
        {
            $hlabel = $this->encodeFormula(iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $this->get_form_field_label($fields[$i],$form)));
            echo '"'.str_replace('"','""', $this->clean_csv_value($hlabel)).'",';
        }
        echo "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
            {
                if (!isset($item[$i]))
                    $item[$i] = '';
                if (is_array($item[$i]))
                    $item[$i] = implode($item[$i],',');
                $item[$i] = $this->encodeFormula(iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $item[$i]));
                echo '"'.str_replace('"','""', $this->clean_csv_value($item[$i])).'",';
            }
            echo "\n";
        }

        exit;
    }
    
    
    public function encodeFormula($text)
    {
        $text = trim($text);
        if ( strlen($text) > 1 && (in_array(substr($text,0,1), array('=','@','+','-'))) )
        {
            if (substr($text,0,1) != '-' || floatval($text)."" != $text)
                $text = chr(9).$text;
        }
        return $text;
    }

    public function setId($id)
    {
        $this->item = $id;
    }


    public function translate_json($str)
    {
        $form_data = json_decode($this->cleanJSON($str));

        $form_data[1][0]->title = __($form_data[1][0]->title,'contact-form-to-email');
        $form_data[1][0]->description = __($form_data[1][0]->description,'contact-form-to-email');

        for ($i=0; $i < count($form_data[0]); $i++)
        {
            $form_data[0][$i]->title = $this->filter_allowed_tags(__($form_data[0][$i]->title,'contact-form-to-email'));
            if (isset($form_data[0][$i]->userhelpTooltip)) $form_data[0][$i]->userhelpTooltip = $this->filter_allowed_tags(__($form_data[0][$i]->userhelpTooltip,'contact-form-to-email'));
            $form_data[0][$i]->userhelp = $this->filter_allowed_tags(__($form_data[0][$i]->userhelp,'contact-form-to-email'));

            $form_data[0][$i]->csslayout = sanitize_text_field($form_data[0][$i]->csslayout);
            
            
            if ($form_data[0][$i]->ftype == 'fCommentArea')
                $form_data[0][$i]->userhelp = __($form_data[0][$i]->userhelp,'contact-form-to-email');
            else
                if ($form_data[0][$i]->ftype == 'fradio' || $form_data[0][$i]->ftype == 'fcheck' || $form_data[0][$i]->ftype == 'fradio')
                {
                    for ($j=0; $j < count($form_data[0][$i]->choices); $j++)
                        $form_data[0][$i]->choices[$j] = __($form_data[0][$i]->choices[$j],'contact-form-to-email');
                }
        }
        $str = json_encode($form_data);
        return $str;
    }


    public function filter_allowed_tags($content)
    {
        //$allowed_tags = wp_kses_allowed_html( 'post' );
        //return  wp_kses( $content, $allowed_tags );
        return  wp_kses( $content, $this->tags_allowed );
    }
    

    private function get_records_csv($formid, $form_name = "")
    {
        global $wpdb;

        $saved_item = $this->item;
        $this->item = intval($formid);

        $last_sent_id = get_option('cp_cfte_last_sent_id_'.$formid, '0');
        $events = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table_messages." WHERE formid=%d AND id>%d ORDER BY id ASC", $formid, $last_sent_id) );

        if ($wpdb->num_rows <= 0) // if no rows, return empty
            return '';

        if ($this->item)
        {
            $form = json_decode($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)));
            $form = $form[0];
        }
        else
            $form = array();

        $buffer = '';
        if ($this->include_user_data_csv)
            $fields = array("Submission ID","Form ID", "Time", "IP Address", "email");
        else
            $fields = array("Submission ID", "Form", "email");
        $values = array();
        foreach ($events as $item)
        {
            if ($this->include_user_data_csv)
                $value = array($item->id, $item->formid, $item->time, $item->ipaddr, $item->notifyto);
            else
                $value = array($item->id, $form_name, $item->notifyto);
            $last_sent_id = $item->id;
            if ($item->posted_data)
                $data = unserialize($item->posted_data);
            else
                $data = array();

            $end = count($fields);
            for ($i=0; $i<$end; $i++)
                if (isset($data[$fields[$i]]) ){
                    $value[$i] = $data[$fields[$i]];
                    unset($data[$fields[$i]]);
                }

            if (is_array($data)) foreach ($data as $k => $d)
            {
               $fields[] = $k;
               $value[] = $d;
            }
            $values[] = $value;
        }
        update_option('cp_cfte_last_sent_id_'.$formid, $last_sent_id);

        $end = count($fields);
        for ($i=0; $i<$end; $i++)
        {
            $hlabel = $this->encodeFormula($this->iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $this->get_form_field_label($fields[$i],$form)));
            $buffer .= '"'.str_replace('"','""', $hlabel).'",';
        }
        $buffer .= "\n";
        foreach ($values as $item)
        {
            for ($i=0; $i<$end; $i++)
            {
                if (!isset($item[$i]))
                    $item[$i] = '';
                if (is_array($item[$i]))
                    $item[$i] = implode(',',$item[$i]);
                $item[$i] = $this->encodeFormula($this->iconv("utf-8", "ISO-8859-1//TRANSLIT//IGNORE", $item[$i]));
                $buffer .= '"'.str_replace('"','""', $item[$i]).'",';
            }
            $buffer .= "\n";
        }

        $this->item = $saved_item;
        return $buffer;

    }

    private function check_reports() {
        global $wpdb;

        $last_verified = get_option('cp_cfte_last_verified','');
        if ( $last_verified == '' || $last_verified < date("Y-m-d H:i:s", strtotime("-1 minutes")) )  // verification to don't check too fast to avoid overloading the site
        {
            update_option('cp_cfte_last_verified',date("Y-m-d H:i:s"));           
            
            $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
            
            // global reports for all forms
            if (get_option('cp_cfte_rep_enable', 'no') == 'yes' && get_option('cp_cfte_rep_days', '') != '' && get_option('cp_cfte_rep_emails', '') != '' )
            {
                $formid = 0;
                $verify_after = date("Y-m-d H:i:s", strtotime("-".get_option('cp_cfte_rep_days', '')." days"));
                $last_sent = get_option('cp_cfte_last_sent'.$formid, '');
                if ($last_sent == '' || $last_sent < $verify_after)  // check if this form needs to check for a new report
                {
                    update_option('cp_cfte_last_sent'.$formid, date("Y-m-d ".(get_option('cp_cfte_rep_hour', '')<'10'?'0':'').get_option('cp_cfte_rep_hour', '').":00:00"));
                    $text = '';
                    $forms = $wpdb->get_results("SELECT id,fp_from_email,form_name,rep_days,rep_hour,rep_emails,rep_subject,rep_emailformat,rep_message,rep_enable FROM ".$wpdb->prefix.$this->table_items); // " WHERE rep_emails<>'' AND rep_enable='yes'"
                    $attachments = array();                    
                    foreach ($forms as $form)  // for each form with the reports enabled
                    {
                        $csv = $this->get_records_csv($form->id, $form->form_name);
                        if ($csv != '')
                        {
                            $text = "- ".substr_count($csv,",\n\"").' submissions from '.$form->form_name."\n";
                            $filename = sanitize_file_name($form->form_name).'_'.date("m_d_y");
                            $filename = $tmp_dir . '/'.$filename .'.csv';
                            $handle = fopen($filename, 'w');
                            fwrite($handle,$csv);
                            fclose($handle);
                            $attachments[] = $filename;
                        }
                    }                    
                    if ('html' == get_option('cp_cfte_rep_emailformat','')) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
                    if (count($attachments))
                    {    
                        $from = get_option('cp_cfte_fp_from_email','');
                        if (!strpos($from,">"))
                            $from = '"'.$from.'" <'.$from.'>';
                        wp_mail( str_replace(" ","",str_replace(";",",",get_option('cp_cfte_rep_emails',''))), get_option('cp_cfte_rep_subject',''), get_option('cp_cfte_rep_message','')."\n".$text,
                                    "From: ".$from."\r\n".
                                    $content_type.
                                    "X-Mailer: PHP/" . phpversion(),
                                    $attachments);
                    }
                    foreach ($attachments as $file)
                        @unlink($file);
                }
            }

            // reports for specific forms
            $forms = $wpdb->get_results("SELECT id,form_name,fp_from_email,rep_days,rep_hour,rep_emails,rep_subject,rep_emailformat,rep_message,rep_enable FROM ".$wpdb->prefix.$this->table_items." WHERE rep_emails<>'' AND rep_enable='yes'");
            foreach ($forms as $form)  // for each form with the reports enabled
            {
                $formid = $form->id;
                $verify_after = date("Y-m-d H:i:s", strtotime("-".$form->rep_days." days"));
                $last_sent = get_option('cp_cfte_last_sent'.$formid, '');
                if ($last_sent == '' || $last_sent < $verify_after)  // check if this form needs to check for a new report
                {
                    update_option('cp_cfte_last_sent'.$formid, date("Y-m-d ".($form->rep_hour<'10'?'0':'').$form->rep_hour.":00:00"));
                    $csv = $this->get_records_csv($formid, $form->form_name);
                    if ($csv != '')
                    {
                        $filename = sanitize_file_name($form->form_name).'_'.date("m_d_y");
                        $filename = $tmp_dir . '/'.$filename .'.csv';
                        $handle = fopen($filename, 'w');
                        fwrite($handle,$csv);
                        fclose($handle);
                        $attachments = array( $filename );
                        if ('html' == $form->rep_emailformat) $content_type = "Content-Type: text/html; charset=utf-8\n"; else $content_type = "Content-Type: text/plain; charset=utf-8\n";
                        $from = $form->fp_from_email;
                        if (!strpos($from,">"))
                            $from = '"'.$from.'" <'.$from.'>';                        
                        wp_mail( str_replace(" ","",str_replace(";",",",$form->rep_emails)), $form->rep_subject, $form->rep_message,
                                "From: ".$from."\r\n".
                                $content_type.
                                "X-Mailer: PHP/" . phpversion(),
                                $attachments);
                        foreach ($attachments as $file)
                            @unlink($file);                                
                    }
                }
            } // end foreach
        } // end if
    }  // end check_reports function


    protected function iconv($from, $to, $text)
    {
        if (function_exists('iconv'))
            return iconv($from, $to, $text);
        else
            return $text;
    }


} // end class


?>