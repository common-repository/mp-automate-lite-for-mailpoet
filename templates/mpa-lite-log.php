<h1><?php _e('Log Settings', 'mp-automate-lite'); ?></h1>
<div class="notice notice-error mpa-log-error" style="display:none;margin-left:0px;padding:10px;">
	<?php _e('There was some error in log settings', 'mp-automate-lite'); ?>
</div>
<div class="notice notice-success mpa-log-success" style="display:none;margin-left:0px;padding:10px;">
	<?php _e('Log settings saved successfully', 'mp-automate-lite'); ?>
</div>
<form method="post" id="mailpoet-log-form">
<div style="display:flex;margin:20px 0;">
	<span style="padding-right:20px;"><?php _e('Enable Log','mp-automate-lite'); ?></span>
	<input type="hidden" name="mpa_lite_log" value="no" />
	<label class="switch">
		<input name="mpa_lite_log" type="checkbox" <?php checked($mpa_log,'yes'); ?> value="yes">
		<span class="slider round"></span>
	</label>
</div>
<?php if(!empty($log_file)): ?>
	<a href="<?php echo esc_url($log_file); ?>" target='_blank'><?php _e('View log','mp-automate-lite');?></a>
	<a href="" id="mpa_lite_log_reset"><?php _e('Reset log','mp-automate-lite');?></a>
	<span id="mpa-reset-success" style="color:green;display:none;"><i><?php _e('Log file has been reset successfully','mp-automate-lite');?></i></span>
<?php endif; ?>
<input type="submit" id="mpa-lite-save-log-settings" style="margin-top:1em;display:block;" class="button button-primary" value="<?php _e('Save Log Settings', 'mp-automate-lite'); ?>"/>
</form>