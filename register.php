<?php

require('lib/common.php');

$error = '';
if ($_POST['register'])
{
	$username = trim($_POST['name']);
	
	if ($username == '')
		$error = 'Please enter an username.';
	elseif ($_POST['pass1'] == '')
		$error = 'Please enter a password.';
	elseif ($_POST['pass1'] != $_POST['pass2'])
		$error = 'The passwords you entered don\'t match.';
	elseif (strlen($_POST['pass1']) < 6)
		$error = 'The password you entered is too short to be secure. It should be atleast 6 characters.';
	else
	{
		$unmatches = SqlQueryResult("SELECT COUNT(*) FROM users WHERE name='".SqlEscape($username)."'");
		$ipmatches = SqlQueryResult("SELECT COUNT(*) FROM users WHERE ip='".SqlEscape($_SERVER['REMOTE_ADDR'])."'");
		
		if ($unmatches)
			$error = 'This username is already taken, please choose another.';
		elseif ($ipmatches)
			$error = 'Another user is using the same IP address as yours.';
		else
		{
			$username = SqlEscape($username);
			$password = hash('sha256', $_POST['pass1'].PASS_SALT);
			$sex = (int)$_POST['sex'];
			if (($sex < 0) || ($sex > 2)) $sex = 2;
			
			$power = 0;
			if (SqlQueryResult("SELECT COUNT(*) FROM users") == 0) $power = 3;
			
			SqlQuery("INSERT INTO users (name,password,powerlevel,sex,regdate,ip) VALUES ('{$username}', '{$password}', {$power}, {$sex}, UNIX_TIMESTAMP(), '".SqlEscape($_SERVER['REMOTE_ADDR'])."')");
			$user = SqlInsertId();
			
			$loginstr = hash('sha256', $user.'|'.$password.'|'.PASS_SALT);
			setcookie('login', base64_encode($user.'|'.$loginstr), time()+999999);
			die(header('Location: index.php'));
		}
	}
}

BuildHeader(array('title' => 'Register'));

$crumbs = BuildCrumbs(array('./'=>'Main', 'lol'=>'Register'));
print $crumbs;

if ($error)
	MsgError($error);

?>
	<form action="register.php" method="post">
		<table class="ptable">
			<tr>
				<th colspan=2>Crendetials</td>
			</tr>
			<tr>
				<td class="c1 center bold" style="width: 150px;">(*) User name:</td>
				<td class="c2 left"><input type="text" name="name" size=20 maxlength=20 value="<?php echo htmlspecialchars($_POST['name']); ?>"></td>
			</tr>
			<tr>
				<td class="c1 center bold">(*) Password:</td>
				<td class="c2 left"><input type="password" name="pass1" size=20 maxlength=32></td>
			</tr>
			<tr>
				<td class="c1 center bold">(*) Confirm password:</td>
				<td class="c2 left"><input type="password" name="pass2" size=20 maxlength=32></td>
			</tr>
			<tr>
				<th colspan=2>Personal settings</td>
			</tr>
			<tr>
				<td class="c1 center bold">Sex:</td>
				<td class="c2 left">
					<label><input type="radio" name="sex" value=1 <?php echo ($_POST['sex'] == 1) ? ' checked="checked"' : ''; ?>> Male</label>
					<label><input type="radio" name="sex" value=2 <?php echo ($_POST['sex'] == 2) ? ' checked="checked"' : ''; ?>> Female</label>
					<label><input type="radio" name="sex" value=0 <?php echo ($_POST['sex'] == 0) ? ' checked="checked"' : ''; ?>> N/A</label>
				</td>
			</tr>
			<tr>
				<th colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td class="c1">&nbsp;</td>
				<td class="c2 left"><input type="submit" name="register" value="Register"></td>
			</tr>
			<tr>
				<td class="c1 left smaller" colspan=2>(*): The fields marked with an asterisk are required.</td>
			</tr>
		</table>
	</form>
<?php

print $crumbs;

BuildFooter();

?>