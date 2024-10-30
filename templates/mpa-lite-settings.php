<div style="display:flex;">
<h1><?php _e('Automation Rules', 'mp-automate-lite'); ?></h1>
<input type="submit" id="mpa-lite-add-rule" class="button button-primary" style="margin:0.67em;" value="<?php _e('Add New Rule', 'mp-automate-lite'); ?>"/>
</div>
<div class="notice notice-error automation-rule-error" style="display:none;margin-left:0px;padding:10px;">
	<?php _e('There was some error in saving Automation rules', 'mp-automate-lite'); ?>
</div>
<div class="notice notice-error upgrade" style="display:none;margin-left:0px;padding:10px;">
    <?php _e('Upgrade to Pro version to create multiple rules on', 'mp-automate-lite');
    ?>
    <a href=<?php esc_url("https://mailpoetautomate.com"); ?> target="_blank">https://mailpoetautomate.com</a>
</div>
<div class="notice notice-success automation-rule-success" style="display:none;margin-left:0px;padding:10px;">
	<?php _e('Automation rules saved successfully', 'mp-automate-lite'); ?>
</div>
<form method="post" id="mpa-lite-rules-form">
	<div style="display:flex;">
	<label for="mpa_lite_run">
		<?php _e('Run automation rules every', 'mp-automate-lite'); ?>
	</label>
	<select name="mpa_lite_run" id="mpa_lite_run" style="margin-left:5px;">
		<option value="-1" <?php selected($automation_run, '-1'); ?>><?php _e('Select the frequency','mp-automate-lite'); ?></option>
		<option value="daily" <?php selected($automation_run, 'daily'); ?>><?php _e('Day','mp-automate-lite'); ?></option>
		<option value="month" <?php selected($automation_run, 'month'); ?>><?php _e('Month','mp-automate-lite'); ?></option>
	</select>
	</div>
	<table border="0" id="mpa-lite-rules-table">
		<?php if(!$automation_rules){
		?>
		<tr id="single-rule" style="display:none;">
			<td>
				<input type="hidden" name="automate_rules[0][active]" value="no" />
				<label class="switch">
					<input name="automate_rules[0][active]" type="checkbox" value="yes">
					<span class="slider round"></span>
				</label>
			</td>
			<td>
			<select name="automate_rules[0][action]">
				<option value="-1"><?php _e('Select an action','mp-automate-lite');?></option>
				<option value="remove"><?php _e('Remove subscriber from','mp-automate-lite');?></option>
				<option value="add"><?php _e('Add subscriber to','mp-automate-lite');?></option>
			</select>
			</td>
			<td>
			<select name="automate_rules[0][list1]">
				<?php foreach( $sagments as $key => $value): ?>
				<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
				<?php endforeach; ?>
			</select>
			</td>
			<td><?php _e('when subscribed to','mp-automate-lite'); ?>
			<select name="automate_rules[0][list2]">
				<?php foreach( $sagments as $key => $value ): ?>
				<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
				<?php endforeach; ?>
			</select>
			</td>
			<td>
				<span class="add-automation-rule dashicons dashicons-plus"></span>
				<span class="remove-automation-rule dashicons dashicons-minus"></span>
			</td>
		</tr>
		<?php	
		} else {
			foreach($automation_rules as $rule_key => $rule_data) {
				$action = $rule_data['action'];
				$active = $rule_data['active'];
				?>
				<tr id="single-rule" class="mp-rule-added">
					<td>
						<input type="hidden" name="automate_rules[<?php echo $rule_key; ?>][active]" value="no" />
						<label class="switch">
							<input type="checkbox" name="automate_rules[<?php echo $rule_key; ?>][active]" <?php checked($active,'yes'); ?> value="yes">
							<span class="slider round"></span>
						</label>
					</td>
					<td>
						<select name="automate_rules[<?php echo $rule_key; ?>][action]">
						<option value="-1"><?php _e('Select an action','mp-automate-lite');?></option>
						<option value="remove" <?php selected($action, "remove"); ?>><?php _e('Remove subscriber from','mp-automate-lite');?></option>
						<option value="add" <?php selected($action, "add"); ?>><?php _e('Add subscriber to','mp-automate-lite');?></option>
						</select>
					</td>
					<td>
					<select name="automate_rules[<?php echo $rule_key; ?>][list1]">
						<?php foreach( $sagments as $key => $value): ?>
						<?php 
						$list1_selected = $list2_selected = "";
						if($rule_data['list1'] === $value['id'])
						$list1_selected = "selected";
						?>
						<option value="<?php echo $value['id']; ?>" <?php echo $list1_selected;?>><?php echo $value['name']; ?></option>
						<?php endforeach; ?>
					</select>
					</td>
					<td><?php _e('when subscribed to','mp-automate-lite'); ?>
					<select name="automate_rules[<?php echo $rule_key; ?>][list2]">
						<?php foreach( $sagments as $key => $value ): ?>
						<?php 
						$list1_selected = $list2_selected = "";
						if($rule_data['list2'] === $value['id'])
						$list2_selected = "selected";
						?>
						<option value="<?php echo $value['id']; ?>" <?php echo $list2_selected;?>><?php echo $value['name']; ?></option>
						<?php endforeach; ?>
					</select>
					</td>
					<td>
						<span class="add-automation-rule dashicons dashicons-plus"></span>
						<span class="remove-automation-rule dashicons dashicons-minus"></span>
					</td>
				</tr>
				<?php
				break;
			}
		?>
			
		<?php
		}?>
	</table>
	<input type="submit" id="mpa-lite-save-automation-rules" style="margin-top:1em;display:block;" class="button button-primary" value="<?php _e('Save Automation Rules', 'mp-automate-lite'); ?>"/>
</form>