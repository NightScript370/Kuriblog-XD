<?php

require('lib/admincommon.php');

if ($_POST['apply'])
{
	SqlQuery("UPDATE misc SET value='".SqlEscape($_POST['sitetitle'])."' WHERE field='sitename'");
	SqlQuery("UPDATE misc SET value='".SqlEscape($_POST['metadescr'])."' WHERE field='metadescr'");
	SqlQuery("UPDATE misc SET value='".SqlEscape($_POST['metakeywords'])."' WHERE field='metakeywords'");
	SqlQuery("UPDATE misc SET value='".($_POST['guestcomments'] ? 1:0)."' WHERE field='guestcomments'");
	
	die(header('Location: admin.php'));
}

BuildHeader(array('title' => 'Admin'));
BuildAdminBar('admin');

?>
	<form action="" method="post">
		<table class="ptable">
			<tr>
				<th colspan=2>General settings</td>
			</tr>
			<tr>
				<td class="c1 center bold" style="width: 150px;">Site name:</td>
				<td class="c2 left"><input type="text" name="sitetitle" size=32 maxlength=200 value="<?php echo SITE_TITLE; ?>"></td>
			</tr>
			<tr>
				<td class="c1 center bold">Meta description:</td>
				<td class="c2 left"><input type="text" name="metadescr" size=32 maxlength=200 value="<?php echo META_DESCR; ?>"></td>
			</tr>
			<tr>
				<td class="c1 center bold">Meta keywords:</td>
				<td class="c2 left"><input type="text" name="metakeywords" size=32 maxlength=200 value="<?php echo META_KEYWORDS; ?>"></td>
			</tr>
			<tr>
				<td class="c1 "></td>
				<td class="c2 left">
					<label><input type="checkbox" name="guestcomments" value=1 <?php echo GUESTCOMMENTS ? ' checked="checked"' : ''; ?>> Allow guests to post comments</label>
				</td>
			</tr>
			<tr>
				<th colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td class="c1">&nbsp;</td>
				<td class="c2 left"><input type="submit" name="apply" value="Apply changes"></td>
			</tr>
		</table>
	</form>
<?php

BuildFooter();

?>