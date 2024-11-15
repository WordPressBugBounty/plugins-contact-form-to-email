<?php

if ( !is_admin() ) 
{
    echo 'Direct access not allowed.';
    exit;
}

$nonce = wp_create_nonce( 'cfpoll_update_actions_post' );

$this->item = intval($_GET["cal"]);
    

define('CP_CFEMAIL_DEFAULT_fp_from_email', get_the_author_meta('user_email', get_current_user_id()) );
define('CP_CFEMAIL_DEFAULT_fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_from_email);

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST[$this->prefix.'_post_options'] ) )
    echo "<div id='setting-error-settings_updated' class='updated settings-error'> <p><strong>Settings saved.</strong></p></div>";

?>
<script type="text/javascript">

	jQuery(function(){
		var $ = jQuery;
		$(document).on('click', '.ahb-step', function(){
			var s = $(this).data('step');
			ahbGoToStep(s);
		});

		window['ahbGoToStep'] = function(s){
			$('.ahb-step.ahb-step-active').removeClass('ahb-step-active');
			$('.ahb-step[data-step="'+s+'"]').addClass('ahb-step-active');
			$('.ahb-adintsection.ahb-adintsection-active').removeClass('ahb-adintsection-active');
			$('.ahb-adintsection[data-step="'+s+'"]').addClass('ahb-adintsection-active');
            $(window).scrollTop( $("#topadminsection").offset().top );
		};
	});

</script>
<div class="wrap">
<h1><?php esc_html_e('Edit','cfte'); ?> - <?php echo esc_html($this->get_option('form_name','Contact Form')); ?> - <?php echo esc_html($this->plugin_name); ?></h1>


<form method="post" action="" name="cpformconf"> 
<input name="<?php echo esc_attr($this->prefix); ?>_post_options" type="hidden" value="1" />
<input name="rsave" type="hidden" value="<?php echo esc_attr($nonce); ?>" />
<input name="<?php echo esc_attr($this->prefix); ?>_id" type="hidden" value="<?php echo intval($this->item); ?>" />


<div id="topadminsection"  class="ahb-buttons-container">
	<input type="submit" class="button button-primary ahb-save-btn" name="savereturn" value="<?php esc_html_e('Save Changes and Return','cfte'); ?>"  />
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php esc_html_e('Return to the contact forms list','cfte'); ?></a>
	<div class="clear"></div>
</div>

   
<div class="ahb-adintsection-container">
	<div class="ahb-breadcrumb">
		<div class="ahb-step ahb-step-active" data-step="1">
			<i>1</i>
			<label><?php esc_html_e('Email Settings','cfte'); ?></label>
		</div>
		<div class="ahb-step" data-step="2">
			<i>2</i>
			<label><?php esc_html_e('Form Builder','cfte'); ?></label>
		</div>
		<div class="ahb-step" data-step="3">
			<i>3</i>
			<label><?php esc_html_e('Autoreply to Customer','cfte'); ?></label>
		</div>
        <div class="ahb-step" data-step="4">
			<i>4</i>
			<label><?php esc_html_e('Texts','cfte'); ?></label>
		</div>        
		<div class="ahb-step" data-step="5">
			<i>5</i>
			<label><?php esc_html_e('Antispam','cfte'); ?></label>
		</div>
		<div class="ahb-step" data-step="6">
			<i>6</i>
			<label><?php esc_html_e('Reports','cfte'); ?></label>
		</div>
	</div>


<div class="ahb-adintsection ahb-adintsection-active" data-step="1">
  <div class="inside">
     <table class="form-table">    
     
        <tr valign="top">
        <th scope="row">Send email "From" </th>
        <td>
          <?php $option = $this->get_option('fp_emailfrommethod', "fixed"); ?>
           <select name="fp_emailfrommethod">
             <option value="fixed"<?php if ($option == 'fixed') echo ' selected'; ?>>From fixed email address indicated below - Recommended option</option>
             <option value="customer"<?php if ($option == 'customer') echo ' selected'; ?>>From the email address indicated by the customer</option>
            </select><br />
            <span style="font-size:10px;color:#666666">
            * If you select "from fixed..." the customer email address will appear in the "to" address when you hit "reply", this is the recommended setting to avoid mail server restrictions. 
            <br />
            * If you select "from customer email" then the customer email will appear also visually when you receive the email, but this isn't supported by all hosting services, so this
            option isn't recommended in most cases.
            </span>
        </td>
        </tr>       
        <tr valign="top">
        <th scope="row">"From" email</th>
        <td><input required type="text" name="fp_from_email" size="40" value="<?php echo esc_attr($this->get_option('fp_from_email', CP_CFEMAIL_DEFAULT_fp_from_email)); ?>" /><br />
        <span style="font-size:10px;color:#666666">
            * Email used as "from". Example: admin@<?php echo str_replace('www.','',$_SERVER["HTTP_HOST"]); ?> 
         </span>
        </td>
        </tr>             
        <tr valign="top">
        <th scope="row">Destination/administrator email</th>
        <td><input required type="text" name="fp_destination_emails" size="40" value="<?php echo esc_attr($this->get_option('fp_destination_emails', CP_CFEMAIL_DEFAULT_fp_destination_emails)); ?>" /><br />
         <span style="font-size:10px;color:#666666">
            * Email that will receive the notification. Comma separated for multiple emails.
         </span>
         </td>
        </tr>
        
        <tr>
         <td colspan="2">
            <div style="border:1px dotted black;padding:5px 15px 5px 15px;font-size:75%;background-color:#ffffcc">
               <p><strong><u>Important note:</u></strong> 
               It is <strong>strongly recommended to use a "from" email address that belongs to the website domain name</strong>, for example if your website is
               <em>http://<?php echo htmlentities($_SERVER["HTTP_HOST"]); ?></em> then use an email address like <em>admin@<?php echo str_replace('www.','',$_SERVER["HTTP_HOST"]); ?></em>, this will help to skip a lot of anti-spam restrictions.
               Avoid using emails like "<em>...@gmail.com</em>", "<em>...@hotmail.com</em>", "<em>...@aol.com</em>" as "from" addresses since these are identified as originated
               at external servers and reach to the spam folder or are completely blocked. This isn't a mandatory requirement but it is strongly recommended.</p>       
            </div>
         </td>
        </tr>     
        
        <tr><td colspan="2"><hr /></td></tr>
        
        <tr valign="top">
        <th scope="row">On submit action</th>
        <td>
           <?php $option = $this->get_option('onsubmitaction', '3'); ?>
           <select name="onsubmitaction" onchange="javascript:fte_action_display();">
             <option value="0"<?php if ($option == '0' || $option == '') echo ' selected'; ?>>Redirect to a "Thank you" page</option>
             <option value="1"<?php if ($option == '1') echo ' selected'; ?>>Stay on the page, display a classic JavaScript alert box with a message</option>
             <option value="2"<?php if ($option == '2') echo ' selected'; ?>>Stay on the page, display a jQuery dialog with a message</option>
             <option value="3"<?php if ($option == '3') echo ' selected'; ?>>Replace form with a message</option>
           </select>
        </td>
        </tr>    
        <tr valign="top" id="opthank">
        <th scope="row">Thank you page (after sending the message)</th>
        <td><input type="text" name="fp_return_page" size="70" value="<?php echo esc_attr($this->get_option('fp_return_page', CP_CFEMAIL_DEFAULT_fp_return_page)); ?>" /></td>
        </tr>  
        <tr valign="top" id="opmsg">
        <th scope="row">Message to display after submission</th>
        <td><textarea name="fp_return_message" cols="80" rows="3"><?php echo esc_textarea($this->get_option('fp_return_message', 'Your message has been successfully sent. Thank you!')); ?></textarea></td>
        </tr> 
        
        <tr><td colspan="2"><hr /></td></tr>
        
        <tr valign="top">
        <th scope="row">Enable notification email to administrator?</th>
        <td>
          <?php $option = $this->get_option('fp_enableemail', 'true'); ?>
          <select name="fp_enableemail"  onchange="javascript:fte_enable_display();">
           <option value="true"<?php if ($option != 'false') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
          </select>
        </td>
        </tr>        
        <tr valign="top" id="fpemail1">
        <th scope="row">Email subject</th>
        <td><input type="text" name="fp_subject" size="70" value="<?php echo esc_attr($this->get_option('fp_subject', CP_CFEMAIL_DEFAULT_fp_subject)); ?>" /></td>
        </tr>
        <tr valign="top" id="fpemail2">
        <th scope="row">Include additional information?</th>
        <td>
          <?php $option = $this->get_option('fp_inc_additional_info', CP_CFEMAIL_DEFAULT_fp_inc_additional_info); ?>
          <select name="fp_inc_additional_info">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
          </select>
        </td>
        </tr>                           
        <tr valign="top" id="fpemail3">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = $this->get_option('fp_emailformat', CP_CFEMAIL_DEFAULT_email_format); ?>
          <select name="fp_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>        
        <tr valign="top" id="fpemail4">
        <th scope="row">Message</th>
        <td><textarea type="text" name="fp_message" rows="6" cols="80"><?php echo esc_textarea($this->get_option('fp_message', CP_CFEMAIL_DEFAULT_fp_message)); ?></textarea></td>
        </tr>                                                               
     </table>  
     
  </div>   
  		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Editor >','cfte'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(2);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>
 </div>   
 

<div class="ahb-adintsection" data-step="2">
  <div class="inside">   

     <input type="hidden" name="form_structure_control" id="form_structure_control" value="&quot;&quot;&quot;&quot;&quot;&quot;" />
     <input type="hidden" name="form_structure" id="form_structure" size="180" value="<?php echo str_replace('"','&quot;',str_replace("\r","",str_replace("\n","",esc_attr($this->cleanJSON($this->get_option('form_structure', CP_CFEMAIL_DEFAULT_form_structure)))))); ?>" />             
        
     <script type="text/javascript">                 
       if (typeof jQuery === "undefined") {
          // This code won't be used in most cases. This code is for preventing problems in wrong WP themes and conflicts with third party plugins.                  
          // In some cases a third party plugin or WP theme affect the expected jQuery scripts, this code autodetect that and provide an alternative solution
          document.write ("<"+"script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></"+"script>");
          document.write ("<"+"script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.20/jquery-ui.min.js'></"+"script>");
       }
       $easyFormQuery = jQuery.noConflict();
       if (typeof $easyFormQuery == 'undefined')
       {
          // This code won't be used in most cases. This code is for preventing problems in wrong WP themes and conflicts with third party plugins.                  
          // In some cases a third party plugin or WP theme affect the expected jQuery scripts, this code autodetect that and provide an alternative solution          
          document.write ("<"+"script type='text/javascript' src='<?php echo plugins_url('js/jquery.validate.js', __FILE__); ?>'></"+"script>");         
          document.write ("<"+"script type='text/javascript' src='<?php echo plugins_url('js/fbuilderf.jquery.js?nc=1', __FILE__); ?>'></"+"script>");         
       } 
     </script> 
             
     <script>
         jQuery(window).on('load', function(){
         $easyFormQuery(document).ready(function() {
            var f = $easyFormQuery("#fbuilder").CFTEfbuilder();
            f.fBuild.loadData("form_structure");
            
            $easyFormQuery("#saveForm").click(function() {       
                f.fBuild.saveData("form_structure");
            });  
                 
            $easyFormQuery(".itemForm").click(function() {
     	       f.fBuild.addItem($easyFormQuery(this).attr("id"));
     	   });  
          
           $easyFormQuery( ".itemForm" ).draggable({revert1: "invalid",helper: "clone",cursor: "move"});
     	   $easyFormQuery( "#fbuilder" ).droppable({
     	       accept: ".button",
     	       drop: function( event, ui ) {
     	           f.fBuild.addItem(ui.draggable.attr("id"));				
     	       }
     	   });
     		    
         }); 
         
        });
        
        var randcaptcha = 1;
        function generateCaptcha()
        {            
           var d=new Date();
           var f = document.cpformconf;    
		   var cv_background = f.cv_background.value;
		   cv_background = cv_background.replace('#','');
		   var cv_border = f.cv_border.value;
		   cv_border = cv_border.replace('#','');
           var qs = "&width="+f.cv_width.value;
           qs += "&height="+f.cv_height.value;
           qs += "&letter_count="+f.cv_chars.value;
           qs += "&min_size="+f.cv_min_font_size.value;
           qs += "&max_size="+f.cv_max_font_size.value;
           qs += "&noise="+f.cv_noise.value;
           qs += "&noiselength="+f.cv_noise_length.value;
           qs += "&bcolor="+cv_background;
           qs += "&border="+cv_border;
           qs += "&font="+f.cv_font.options[f.cv_font.selectedIndex].value;
           qs += "&r="+(randcaptcha++);
           
           document.getElementById("captchaimg").src= "<?php echo $this->get_site_url_slash(true).$this->prefix.'_captcha=captcha&inAdmin=1'; ?>"+qs;
        }
        
        function fte_action_display()
        {
           var f = document.cpformconf; 
           var selection = f.onsubmitaction.options[f.onsubmitaction.selectedIndex].value;
           if (selection == '0')
           {
               document.getElementById("opthank").style.display = '';
               document.getElementById("opmsg").style.display = 'none';
           }
           else
           {
               document.getElementById("opthank").style.display = 'none';
               document.getElementById("opmsg").style.display = '';
           }    
        }
        fte_action_display();
        
        function fte_enable_display()
        {
           var f = document.cpformconf; 
           var selection = f.fp_enableemail.options[f.fp_enableemail.selectedIndex].value;
           if (selection != 'false')
           {
               document.getElementById("fpemail1").style.display = '';
               document.getElementById("fpemail2").style.display = '';
               document.getElementById("fpemail3").style.display = '';
               document.getElementById("fpemail4").style.display = '';
           }
           else
           {
               document.getElementById("fpemail1").style.display = 'none';
               document.getElementById("fpemail2").style.display = 'none';
               document.getElementById("fpemail3").style.display = 'none';
               document.getElementById("fpemail4").style.display = 'none';
           }    
        }
        fte_enable_display();        
        

     </script>
     
     <div style="background:#fafafa;min-width:600px;" class="form-builder">
     
         <div class="column width50">
             <div id="tabs">
     			<ul>
     				<li><a href="#tabs-1">Add a Field</a></li>
     				<li><a href="#tabs-2">Field Settings</a></li>
     				<li><a href="#tabs-3">Form Settings</a></li>
     			</ul>
     			<div id="tabs-1">
     			    
     			</div>
     			<div id="tabs-2"></div>
     			<div id="tabs-3"></div>
     		</div>	
         </div>
         <div class="columnr width50 padding10" id="fbuilder">
             <div id="formheader"></div>
             <div id="fieldlist"></div>
             <div class="button" id="saveForm">Save Form</div>
         </div>
         <div class="clearer"></div>
         
     </div>        
   
  <div style="padding:10px;background-color:#ffffdd;border:1px dotted black;">
   <p><STRONG>In this version</STRONG> the form builder supports <STRONG>"Single Line Text", "Email", "Text-area" and "Acceptance Checkbox"</STRONG>.</p>
   <p><button type="button" onclick="window.open('https://form2email.dwbooster.com/download?src=activatebtn');" style="cursor:pointer;height:35px;color:#20A020;font-weight:bold;">Activate the FULL form builder</button>
   <p style="font-weight:bold">The full set of fields also supports:</p>
   <ul>
    <li> - <strong>Conditional Logic</strong>: Hide/show fields based in previous selections.</li>
    <li> - File <strong>uploads</strong>, <strong>Multi-page</strong> forms, sidebar widget</li>
    <li> - <strong>Convert the form in a payment / booking form</strong> with integration with PayPal Standard, PayPal Pro, Stripe, Authorize.net, Skrill, Mollie / iDeal, TargetPay / iDeal, SagePay, RedSys TPV and Sage Payments.</li>
    <li> - <strong><a href="?page=cp_apphourbooking_addons">Full set of addons</a></strong> (iCal, SMS, Signature fields, Payment Calculations, reCaptcha, MailChimp, ...), <strong>fields</strong> and <strong>validations</strong></li>
   </ul>
   <p>For an appointment booking option check the <a href="https://wordpress.org/plugins/appointment-hour-booking/">Appointment/Service Booking Calendar</a>.</p>
   </p>
   
  </div>
   
  </div>  
    	<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Autoreply to Customer >','cfte'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(3);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>
 </div> 
 

 <div class="ahb-adintsection" data-step="4">
 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Submit Button</span></h3>
  <div class="inside">   
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Submit button label (text):</th>
        <td><input type="text" name="vs_text_submitbtn" size="40" value="<?php $label = esc_attr($this->get_option('vs_text_submitbtn', 'Submit')); echo ($label==''?'Submit':$label); ?>" /></td>
        </tr>
        <tr valign="top" class="cftesoft">
        <th scope="row">Previous button label (text):</th>
        <td><input type="text" name="vs_text_previousbtn" size="40" value="<?php $label = esc_attr($this->get_option('vs_text_previousbtn', 'Previous')); echo ($label==''?'Previous':$label); ?>" /></td>
        </tr>    
        <tr valign="top" class="cftesoft">
        <th scope="row">Next button label (text):</th>
        <td><input type="text" name="vs_text_nextbtn" size="40" value="<?php $label = esc_attr($this->get_option('vs_text_nextbtn', 'Next')); echo ($label==''?'Next':$label); ?>" /></td>
        </tr>  
        <tr valign="top" class="cftesoft">
        <th scope="row">Page {0} of {0} (text):</th>
        <td><input type="text" name="vs_text_page" size="6" value="<?php $label = esc_attr($this->get_option('vs_text_page', 'Page')); echo ($label==''?'Page':$label); ?>" />
            X 
            <input type="text" name="vs_text_of" size="3" value="<?php $label = esc_attr($this->get_option('vs_text_of', 'of')); echo ($label==''?'of':$label); ?>" />
            Y</td>
        </tr>         
        <tr valign="top">
        <td colspan="2"> - The  <em>class="pbSubmit"</em> can be used to modify the button styles. <br />
        - The styles can be applied into the <a href="?page=cp_contactformtoemail&edit=1&cal=<?php echo intval($this->item); ?>&item=css">CSS Customization Area</a>. <br />
        - For further modifications the submit button is located at the end of the file <em>"cp-public-int.inc.php"</em>.<br />
        - For general CSS styles modifications to the form and samples <a href="https://form2email.dwbooster.com/faq#q82" target="_blank">check this FAQ</a>.
        </tr>
     </table>
  </div>    
 </div> 
 

 <div id="metabox_basic_settings" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span>Validation Settings</span></h3>
  <div class="inside">
     <table class="form-table">    
        <tr valign="top" style="display:none">
        <th scope="row">Use Validation?</th>
        <td>
          <?php $option = $this->get_option('vs_use_validation', CP_CFEMAIL_DEFAULT_vs_use_validation); ?>
          <select name="vs_use_validation">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
          </select>
        </td>
        </tr>
        <tr valign="top">
        <td scope="row">"is required" text:<br /><input type="text" required name="vs_text_is_required" size="40" value="<?php echo esc_attr($this->get_option('vs_text_is_required', CP_CFEMAIL_DEFAULT_vs_text_is_required)); ?>" /></td>
        </tr>             
         <tr valign="top">
        <td scope="row">"is email" text:<br /><input type="text" required name="vs_text_is_email" size="50" value="<?php echo esc_attr($this->get_option('vs_text_is_email', CP_CFEMAIL_DEFAULT_vs_text_is_email)); ?>" /></td>
        <td scope="row">"is valid captcha" text:<br /><input type="text" name="cv_text_enter_valid_captcha" size="50" value="<?php echo esc_attr($this->get_option('cv_text_enter_valid_captcha', CP_CFEMAIL_DEFAULT_cv_text_enter_valid_captcha)); ?>" /></td>
        </tr>
        <tr valign="top">
        <td scope="row">"is valid date (mm/dd/yyyy)" text:<br /><input type="text" name="vs_text_datemmddyyyy" size="50" value="<?php echo esc_attr($this->get_option('vs_text_datemmddyyyy', CP_CFEMAIL_DEFAULT_vs_text_datemmddyyyy)); ?>" /></td>
        <td scope="row">"is valid date (dd/mm/yyyy)" text:<br /><input type="text" name="vs_text_dateddmmyyyy" size="50" value="<?php echo esc_attr($this->get_option('vs_text_dateddmmyyyy', CP_CFEMAIL_DEFAULT_vs_text_dateddmmyyyy)); ?>" /></td>
        </tr>
        <tr valign="top">
        <td scope="row">"is number" text:<br /><input type="text" name="vs_text_number" size="50" value="<?php echo esc_attr($this->get_option('vs_text_number', CP_CFEMAIL_DEFAULT_vs_text_number)); ?>" /></td>
        <td scope="row">"only digits" text:<br /><input type="text" name="vs_text_digits" size="50" value="<?php echo esc_attr($this->get_option('vs_text_digits', CP_CFEMAIL_DEFAULT_vs_text_digits)); ?>" /></td>
        </tr>
        <tr valign="top">
        <td scope="row">"under maximum" text:<br /><input type="text" name="vs_text_max" size="50" value="<?php echo esc_attr($this->get_option('vs_text_max', CP_CFEMAIL_DEFAULT_vs_text_max)); ?>" /></td>
        <td scope="row">"over minimum" text:<br /><input type="text" name="vs_text_min" size="50" value="<?php echo esc_attr($this->get_option('vs_text_min', CP_CFEMAIL_DEFAULT_vs_text_min)); ?>" /></td>
        </tr>             
        
     </table>  
  </div>    
 </div>   
  		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Antispam >','cfte'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(5);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div> 
 </div>
 
 
 <div class="ahb-adintsection" data-step="3">
  <div class="inside">
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Send confirmation/thank you message to user?</th>
        <td>
          <?php $option = $this->get_option('cu_enable_copy_to_user', CP_CFEMAIL_DEFAULT_cu_enable_copy_to_user); ?>
          <select name="cu_enable_copy_to_user">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
          </select>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Email field on the form</th>
        <td><select id="cu_user_email_field" name="cu_user_email_field" def="<?php echo esc_attr($this->get_option('cu_user_email_field', CP_CFEMAIL_DEFAULT_cu_user_email_field)); ?>"></select></td>
        </tr>             
        <tr valign="top">
        <th scope="row">Email subject</th>
        <td><input type="text" name="cu_subject" size="70" value="<?php echo esc_attr($this->get_option('cu_subject', CP_CFEMAIL_DEFAULT_cu_subject)); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = $this->get_option('cu_emailformat', CP_CFEMAIL_DEFAULT_email_format); ?>
          <select name="cu_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>  
        <tr valign="top">
        <th scope="row">Message</th>
        <td><textarea type="text" name="cu_message" rows="6" cols="80"><?php echo esc_textarea($this->get_option('cu_message', CP_CFEMAIL_DEFAULT_cu_message)); ?></textarea></td>
        </tr>        
     </table>  
  </div> 
  		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Texts >','cfte'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(4);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>  
 </div>  
 

 <div class="ahb-adintsection" data-step="5">
  <div class="inside">

     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Use invisible "intelligent" spam prevention?*</th>
        <td colspan="5">
          <?php $option = $this->get_option('cv_enable_captchaintelligent', CP_CFEMAIL_DEFAULT_cv_enable_captchainvisible); ?>
          <select name="cv_enable_captchaintelligent">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option != 'true') echo ' selected'; ?>>No</option>
          </select><br />
          <em>* Uses invisible spam protection methods (honeypot, smart scripts, etc...). This can be used at the same time than the classic captcha or you can also keep
          only one of these options enabled.</em>
        </td>
        </tr>
     </table>        
     <hr />
     
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Use classic antispam Captcha image Verification?</th>
        <td colspan="5">
          <?php $option = $this->get_option('cv_enable_captcha', CP_CFEMAIL_DEFAULT_cv_enable_captcha); ?>
          <select name="cv_enable_captcha">
           <option value="true"<?php if ($option == 'true') echo ' selected'; ?>>Yes</option>
           <option value="false"<?php if ($option == 'false') echo ' selected'; ?>>No</option>
          </select>
        </td>
        </tr>
        
        <tr valign="top">
         <th scope="row">Width:</th>
         <td><input type="number" name="cv_width" size="10" value="<?php echo esc_attr($this->get_option('cv_width', CP_CFEMAIL_DEFAULT_cv_width)); ?>"  onblur="generateCaptcha();"  /></td>
         <th scope="row">Height:</th>
         <td><input type="number" name="cv_height" size="10" value="<?php echo esc_attr($this->get_option('cv_height', CP_CFEMAIL_DEFAULT_cv_height)); ?>" onblur="generateCaptcha();"  /></td>
         <th scope="row">Chars:</th>
         <td><input type="number" name="cv_chars" size="10" value="<?php echo esc_attr($this->get_option('cv_chars', CP_CFEMAIL_DEFAULT_cv_chars)); ?>" onblur="generateCaptcha();"  /></td>
        </tr>             

        <tr valign="top">
         <th scope="row">Min font size:</th>
         <td><input type="number" name="cv_min_font_size" size="10" value="<?php echo esc_attr($this->get_option('cv_min_font_size', CP_CFEMAIL_DEFAULT_cv_min_font_size)); ?>" onblur="generateCaptcha();"  /></td>
         <th scope="row">Max font size:</th>
         <td><input type="number" name="cv_max_font_size" size="10" value="<?php echo esc_attr($this->get_option('cv_max_font_size', CP_CFEMAIL_DEFAULT_cv_max_font_size)); ?>" onblur="generateCaptcha();"  /></td>        
         <td colspan="2" rowspan="">
           Preview:<br />
             <br />
            <img src="<?php echo $this->get_site_url_slash(true).$this->prefix.'_captcha=captcha&inAdmin=1'; ?>"  id="captchaimg" alt="security code" border="0"  />            
         </td> 
        </tr>             
                

        <tr valign="top">
         <th scope="row">Noise:</th>
         <td><input type="number" name="cv_noise" size="10" value="<?php echo esc_attr($this->get_option('cv_noise', CP_CFEMAIL_DEFAULT_cv_noise)); ?>" onblur="generateCaptcha();" /></td>
         <th scope="row">Noise Length:</th>
         <td><input type="number" name="cv_noise_length" size="10" value="<?php echo esc_attr($this->get_option('cv_noise_length', CP_CFEMAIL_DEFAULT_cv_noise_length)); ?>" onblur="generateCaptcha();" /></td>        
        </tr>          
        

        <tr valign="top">
         <th scope="row">Background:</th>
         <td><input type="color" name="cv_background" size="10" value="#<?php echo esc_attr($this->get_option('cv_background', CP_CFEMAIL_DEFAULT_cv_background)); ?>" onchange="generateCaptcha();" /></td>
         <th scope="row">Border:</th>
         <td><input type="color" name="cv_border" size="10" value="#<?php echo esc_attr($this->get_option('cv_border', CP_CFEMAIL_DEFAULT_cv_border)); ?>" onchange="generateCaptcha();" /></td>        
        </tr>    
        
        <tr valign="top">
         <th scope="row">Font:</th>
         <td>
            <select name="cv_font" onchange="generateCaptcha();" >
              <option value="font1"<?php if ("font1" == $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font)) echo " selected"; ?>>Font 1</option>
              <option value="font2"<?php if ("font2" == $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font)) echo " selected"; ?>>Font 2</option>
              <option value="font3"<?php if ("font3" == $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font)) echo " selected"; ?>>Font 3</option>
              <option value="font4"<?php if ("font4" == $this->get_option('cv_font', CP_CFEMAIL_DEFAULT_cv_font)) echo " selected"; ?>>Font 4</option>
            </select>            
         </td>              
        </tr>                          
           
        
     </table>  
  </div>   
  		<div class="ahb-buttons-container">
			<input type="button" value="<?php esc_html_e('Next Step - Reports >','cfte'); ?>" class="button" style="float:right;margin-right:10px" onclick="ahbGoToStep(6);" />
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>  
 </div>    
 
 <div class="ahb-adintsection" data-step="6">
  <h3 class='hndle' style="padding:5px;"><span>Automatic Reports: Send submissions in CSV format via email</span></h3>
  <div class="inside">

     <p><strong>Note: </strong> This section is for <strong>daily reports</strong> for this form. If you are looking for the <strong>immediate email notifications</strong>
     then go to the <a href="javascript:ahbGoToStep(1);">STEP 1</a> of the settings on this page.</p>
     
     <table class="form-table">    
        <tr valign="top">
        <th scope="row">Enable Reports?</th>
        <td>
          <?php $option = $this->get_option('rep_enable', 'no'); ?>
          <select name="rep_enable">
           <option value="no"<?php if ($option == 'no' || $option == '') echo ' selected'; ?>>No</option>
           <option value="yes"<?php if ($option == 'yes') echo ' selected'; ?>>Yes</option>
          </select>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Send report every</th>
        <td><input type="number" name="rep_days" size="4" value="<?php echo esc_attr($this->get_option('rep_days', '7')); ?>" /> days</td>
        </tr>        
        <tr valign="top">
        <th scope="row">Send report after this hour (server time)</th>
        <td>
          <select name="rep_hour">
           <?php
             $hour = $this->get_option('rep_hour', '0');
             for ($k=0;$k<24;$k++)
                 echo '<option value="'.$k.'"'.($hour==$k?' selected':'').'>'.($k<10?'0':'').$k.'</option>';
           ?>
          </select>
        </td>
        </tr>        
        <tr valign="top">
        <th scope="row">Send the report to the following email addresses (comma separated)</th>
        <td><input type="text" name="rep_emails" size="70" value="<?php echo esc_attr($this->get_option('rep_emails', '')); ?>" /></td>
        </tr>             
        <tr valign="top">
        <th scope="row">Email subject</th>
        <td><input type="text" name="rep_subject" size="70" value="<?php echo esc_attr($this->get_option('rep_subject', 'Submissions report...')); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Email format?</th>
        <td>
          <?php $option = $this->get_option('rep_emailformat', 'text'); ?>
          <select name="rep_emailformat">
           <option value="text"<?php if ($option != 'html') echo ' selected'; ?>>Plain Text (default)</option>
           <option value="html"<?php if ($option == 'html') echo ' selected'; ?>>HTML (use html in the textarea below)</option>
          </select>
        </td>
        </tr>  
        <tr valign="top">
        <th scope="row">Email Text (CSV file will be attached with the submissions)</th>
        <td><textarea type="text" name="rep_message" rows="3" cols="80"><?php echo esc_textarea($this->get_option('rep_message', 'Attached you will find the data from the form submissions.')); ?></textarea></td>
        </tr>        
     </table>  
  </div>   
		<div class="ahb-buttons-container">
			<input type="submit" name="savepublish" value="<?php esc_html_e('Save and Publish','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<input type="submit" name="savereturn" value="<?php esc_html_e('Save and Return','cfte'); ?>" class="button button-primary" style="float:right;margin-right:10px" />
			<div class="clear"></div>
		</div>  
 </div>   

</div> 


 <div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php esc_html_e('Return to the contact forms list','cfte'); ?></a>
 </div>


</form>
</div>

[<a href="https://wordpress.org/support/plugin/contact-form-to-email#new-post" target="_blank">Support</a>] | [<a href="<?php echo $this->plugin_URL; ?>" target="_blank">Help</a>]
<script type="text/javascript">generateCaptcha();</script>