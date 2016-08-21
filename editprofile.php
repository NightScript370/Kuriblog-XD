<?php

require('lib/common.php');

if (!$login)
	Kill('You must be logged in to edit your profile.');
	
if ($mypower < 0)
	Kill('Banned users may not edit their profile.');

if ($mypower >= 3 && isset($_GET['id']))
{
	$adminmode = true;
	$userid = (int)$_GET['id'];
	$user = SqlQueryFetchRow("SELECT * FROM users WHERE id={$userid}");
	if (!$user) Kill('Invalid user ID.');
}
else
{
	$adminmode = false;
	$userid = $myuserid;
	$user = $myuserdata;
}

if ($adminmode) $action = 'Edit user '.htmlspecialchars($user['name']);
else $action = 'Edit profile';

$key = hash('sha256', "{$myuserdata['id']},{$myuserdata['password']},blahblah");
$error = '';
if ($_POST['savechanges'])
{
	if ($_POST['key'] != $key)
		die('No.');

	$newpass = '';
	if ($_POST['changepass'] == 'on')
	{
		if ($_POST['pass1'] != $_POST['pass2'])
			$error = 'The passwords you entered don\'t match.';
		else if ($userid == $myuserid)
			$newpass = 'password=\''.hash('sha256', $_POST['pass1'].PASS_SALT).'\', ';
	}
	
	if (!$error)
	{
		$sex = (int)$_POST['sex'];
		if ($sex<0 || $sex>2) $sex = 2;
		
		$theme = (int)$_POST['theme'];
		
		$adminopts = '';
		if ($adminmode)
		{
			$username = trim($_POST['name']);
			$powerlevel = (int)$_POST['powerlevel'];
			
			$unmatches = SqlQueryResult("SELECT COUNT(*) FROM users WHERE name='".SqlEscape($username)."' AND id!={$userid}");
			if ($unmatches) $error = 'This username is already taken.';
			else
			{
				if ($powerlevel < -1 || $powerlevel > 3) $powerlevel = $user['powerlevel'];
				$adminopts = ", name='".SqlEscape($username)."', powerlevel=".$powerlevel;
			}
		}
	}
	
	if (!$error)
	{
		SqlQuery("UPDATE users SET {$newpass}sex={$sex}, theme={$theme}{$adminopts} WHERE id={$userid}");
	
		die(header('Location: profile.php?id='.$userid));
	}
}
else
{
	$_POST['sex'] = $user['sex'];
	
	if ($adminmode)
	{
		$_POST['name'] = $user['name'];
		$_POST['powerlevel'] = $user['powerlevel'];
	}
}

BuildHeader(array('title' => $action));

$crumbs = BuildCrumbs(array('./'=>'Main', 'lol'=>$action));
print $crumbs;

if ($error)
	MsgError($error);

$themelist = '<select name="theme">';
$themes = SqlQuery("SELECT t.*, (SELECT COUNT(*) FROM users WHERE theme=t.id) lovers FROM themes t ORDER BY id");
while ($theme = SqlFetchRow($themes))
{
	$check = ($myuserdata['theme'] == $theme['id']) ? ' selected="selected"' : '';
	$themelist .= "<option value=\"{$theme['id']}\"{$check}>{$theme['name']} ({$theme['lovers']})</option>";
}
$themelist .= '</select>';
	
?>
	<form action="" method="post" onsubmit="if (this.changepass.checked && (this.pass1.value!=this.pass2.value)) { alert('The passwords you entered don\'t match.'); return false; }">
		<table class="ptable">
			<tr>
				<th colspan=2>Credentials</th>
			</tr>
			<tr>
				<td class="c1" style="width: 155px;">&nbsp;</td>
				<td class="c2 left"><label><input type="checkbox" name="changepass"> Change password</label></td>
			<tr>
				<td class="c1 center bold">New password:</td>
				<td class="c2 left"><input type="password" name="pass1" size=20 maxlength=32 value=""></td>
			</tr>
			<tr>
				<td class="c1 center bold">Confirm new password:</td>
				<td class="c2 left"><input type="password" name="pass2" size=20 maxlength=32 value=""></td>
			</tr>
			
			<tr>
				<th colspan=2>Personal settings</th>
			</tr>
			<tr>
				<td class="c1 center bold">Sex:</td>
				<td class="c2 left">
					<label><input type="radio" name="sex" value=1 <?php if ($_POST['sex']==1) print 'checked="checked" '; ?>/> Male</label>
					<label><input type="radio" name="sex" value=2 <?php if ($_POST['sex']==2) print 'checked="checked" '; ?>/> Female</label>
					<label><input type="radio" name="sex" value=0 <?php if ($_POST['sex']==0) print 'checked="checked" '; ?>/> N/A</label>
				</td>
			</tr>
			
			<tr>
				<th colspan=2>Site appearance</th>
			</tr>
			<tr>
				<td class="c1 center bold">Theme:</td>
				<td class="c2 left">
					<?php print $themelist; ?>
				</td>
			</tr>
			
			<?php if ($adminmode) { ?>
			<tr>
				<th colspan=2>Administrative options</th>
			</tr>
			<tr>
				<td class="c1 center bold">Username:</td>
				<td class="c2 left">
					<input type="text" name="name" size="20" maxlength="20" value="<?php echo htmlspecialchars($_POST['name']); ?>">
				</td>
			</tr>
			<tr>
				<td class="c1 center bold">Rank:</td>
				<td class="c2 left">
					<select name="rank">
						<option value="-1"<?php echo ($_POST['powerlevel'] == -1 ? ' selected="selected"':''); ?>>Banned</option>
						<option value="0"<?php echo ($_POST['powerlevel'] == 0 ? ' selected="selected"':''); ?>>Normal user</option>
						<option value="1"<?php echo ($_POST['powerlevel'] == 1 ? ' selected="selected"':''); ?>>Comments moderator</option>
						<option value="2"<?php echo ($_POST['powerlevel'] == 2 ? ' selected="selected"':''); ?>>Blog poster</option>
						<option value="3"<?php echo ($_POST['powerlevel'] == 3 ? ' selected="selected"':''); ?>>Admin</option>
					</select>
				</td>
			</tr>
			<?php } ?>
			
			<tr>
				<th colspan=2>&nbsp;</th>
			</tr>
			<tr>
				<td class="c1">&nbsp;</td>
				<td class="c2 left"><input type="submit" name="savechanges" value="Save changes"></td>
			</tr>
		</table>
		<input type="hidden" name="key" value="<?php print $key; ?>">
	</form>
<?php

print $crumbs;

BuildFooter();

?>