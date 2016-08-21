<?php

require('lib/common.php');

$error = '';

if (isset($_GET['logout']))
{
	setcookie('login');
	die(header('Location: index.php'));
}
elseif ($_POST['login'])
{
	if (!$_POST['username'] or !$_POST['password'])
		$error = 'Please enter an user name and a password.';
	else
	{
		$password = hash('sha256', $_POST['password'].PASS_SALT);
		$user = SqlQueryResult("SELECT id FROM users WHERE name='".SqlEscape($_POST['username'])."' AND password='{$password}'");
		if (!$user)
			$error = 'Invalid user name or password.';
		else
		{
			$loginstr = hash('sha256', $user.'|'.$password.'|'.PASS_SALT);
			setcookie('login', base64_encode($user.'|'.$loginstr), time()+999999);
			die(header('Location: index.php'));
		}
	}
}

BuildHeader(array('title' => 'Log in'));

$crumbs = BuildCrumbs(array('./'=>'Main', 'lol'=>'Log in'));
print $crumbs;

if ($error)
	MsgError($error);
	
?>
	<form action="login.php" method="post">
		<table class="ptable">
			<tr>
				<th colspan=2>Log in</th>
			</tr>
			<tr>
				<td class="c1 center bold" style="width: 150px;">User name:</td>
				<td class="c2 left"><input type="text" name="username" size=20 maxlength=20 value="<?php echo htmlspecialchars($_POST['username']); ?>"></td>
			</tr>
			<tr>
				<td class="c1 center bold">Password:</td>
				<td class="c2 left"><input type="password" name="password" size=20 maxlength=32></td>
			</tr>
			<tr>
				<td class="c1">&nbsp;</td>
				<td class="c2 left"><input type="submit" name="login" value="Log in"></td>
			</tr>
			<tr>
				<td class="c1 left smaller" colspan=2>Don't have an account? <a href="register.php">Register one now</a>!</td>
			</tr>
		</table>
	</form>
<?php

print $crumbs;

BuildFooter();

?>