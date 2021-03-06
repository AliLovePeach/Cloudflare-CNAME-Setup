<?php
/*
 * Edit a record.
 */

if(!isset($tlo_id)){ exit; }

$dns = new \Cloudflare\API\Endpoints\DNS($adapter);
$dns_details = $dns->getRecordDetails($_GET['zoneid'], $_GET['recordid']);
if (isset($_POST['submit'])) {
	if(isset($_POST['proxied']) && $_POST['proxied'] == 'true'){
		$_POST['proxied'] = true;
	} else {
		$_POST['proxied'] = false;
	}
	$_POST['ttl'] = intval($_POST['ttl']);
	if(isset($_POST['priority'])){
		$_POST['priority'] = intval($_POST['priority']);
	} else {
		$_POST['priority'] = 10;
	}
	try {
		if ( $dns->updateRecordDetails($_GET['zoneid'], $_GET['recordid'], ['type' => $dns_details->type, 'name' => $_POST['name'], 'content' => $_POST['content'], 'ttl' => $_POST['ttl'], 'priority' => $_POST['priority'], 'proxied' => $_POST['proxied']]) ) {
			exit( '<p>'._('Success').', <a href="?action=zones&domain='.$_GET['domain'].'&amp;zoneid='.$_GET['zoneid'].'">'._('Go to console').'</a></p>' );
		} else {
			exit( '<p>'._('Failed').', <a href="?action=zones&domain='.$_GET['domain'].'&amp;zoneid='.$_GET['zoneid'].'">'._('Go to console').'</a></p>' );
		}
	} catch (Exception $e) {
		echo $e;
	}
}
	if( isset($msg) ){ echo $msg; }
	?>
<form method="POST" action="" class="am-form">
	<fieldset>
		<legend><?php echo _('Edit DNS Record'); ?></legend>
		<div class="am-form-group">
			<label for="name"><?php echo _('Record Name (e.g. “@”, “www”, etc.)'); ?></label>
			<input type="text" name="name" id="name" value="<?php echo $dns_details->name ; ?>">
		</div>
		<div class="am-form-group">
			<label for="type"><?php echo _('Record Type'); ?></label>
			<select name="type" id="type" disabled="disabled">
				<option value="<?php echo $dns_details->type ; ?>"><?php echo $dns_details->type ; ?></option>
			</select>
		</div>
		<div class="am-form-group">
			<label for="doc-ta-1"><?php echo _('Record Content'); ?></label>
			<textarea name="content" rows="5" id="doc-ta-1"><?php echo $dns_details->content ; ?></textarea>
		</div>
		<div class="am-form-group">
			<label for="ttl">TTL</label>
			<select name="ttl" id="ttl">
				<?php
				foreach ($ttl_translate as $_ttl => $_ttl_name){
					echo '<option value="'.$_ttl.'">'.$_ttl_name.'</option>';
				}
				?>
			</select>
		</div>
		<?php if($dns_details->proxiable){ ?>
		<div class="am-form-group">
			<label for="proxied">CDN</label>
			<select name="proxied" id="proxied">
				<option value="true" <?php if($dns_details->proxied){ echo 'selected="selected"'; } ?>><?php echo _('On'); ?></option>
				<option value="false" <?php if(!$dns_details->proxied){ echo 'selected="selected"'; } ?>><?php echo _('Off'); ?></option>
			</select>
		</div>
		<?php } ?>
		<?php if($dns_details->type == 'MX'){ ?>
		<div class="am-form-group">
			<label for="priority"><?php echo _('Priority (Only for MX record)'); ?></label>
			<input type="number" name="priority" id="priority" step="1" min="1" value="<?php echo $dns_details->priority ; ?>">
		</div>
		<?php } ?>
		
		<p><button type="submit" name="submit" class="am-btn am-btn-default"><?php echo _('Submit'); ?></button></p>
	</fieldset>
</form>
