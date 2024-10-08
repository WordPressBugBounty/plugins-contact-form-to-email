<?php

$this->item = intval($_GET["cal"]);

$current_user = wp_get_current_user();
$current_user_access = current_user_can('edit_pages');

if ( !is_admin() || (!$current_user_access && !@in_array($current_user->ID, unserialize($this->get_option("cp_user_access","")))))
{
    echo 'Direct access not allowed.';
    exit;
}

$message = '';

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST[$this->prefix.'_pform_process'] ) )
    echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>".'Message updated. Can be checked in the <a href="?page='.$this->menu_parameter.'&cal='.$this->item.'&list=1">messages list</a>.'."</strong></p></div>";

$nonce = wp_create_nonce( 'cfte_actions_admin' );

?>
<style>
	.clear{clear:both;}
	.ahb-first-button{margin-right:10px !important;}
    .ahb-buttons-container{margin:1em 1em 1em 0;}
    .ahb-return-link{float:right;}
</style>
<div class="wrap" id="ahbadminedititem">

<h1><?php _e('Edit Message','contact-form-to-email'); ?></h1>  

<div class="ahb-buttons-container">
	<a href="<?php print esc_attr(admin_url('admin.php?page='.$this->menu_parameter));?>" class="ahb-return-link">&larr;<?php _e('Return to the forms list','contact-form-to-email'); ?></a>
    <div class="clear"></div>
</div>

<p><?php _e('This page is for editing messages from the administration area. The captcha and payment process are disabled in order to allow the website manager easily editing bookings.','contact-form-to-email'); ?></p> 

<?php echo $this->filter_content(array('id' => $this->item, 'prefill' => $_GET["edititem"])); ?>

</div>


