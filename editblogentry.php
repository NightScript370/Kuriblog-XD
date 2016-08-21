<?php

require('lib/common.php');

$id = (int)$_GET['id'];
$new = ($id == 0);

if ($new)
{
	$action = 'post';
	$actioncap = 'New';
}
elseif ($_GET['action'] == 'delete')
{
	if ($_GET['token'] !== $mytoken) Kill('No.');
	$action = 'delete';
	$actioncap = 'Delete';
}
else
{
	$action = 'edit';
	$actioncap = 'Edit';
}

if (!$login)
	Kill('You must be logged in to '.$action.' blog entries.');
	
if ($mypower < 2)
	Kill('You aren\'t allowed to '.$action.' blog entries.');

if (!$new)
{
	$entry = SqlQueryFetchRow("SELECT * FROM blog_entries WHERE id={$id}");
	if (!$entry)
		Kill('Invalid blog entry ID.');
		
	if (($mypower < 3) && ($entry['userid'] != $myuserid))
		Kill('You aren\'t allowed to '.$action.' this blog entry.');
}
else
	$entry = array('userid' => $myuserid, 'title' => '', 'text' => '');
	
$error = '';

if ($_GET['action'] == 'delete')
{
	SqlQuery("DELETE FROM blog_entries WHERE id={$id}");
	SqlQuery("DELETE FROM blog_comments WHERE entryid={$id}");
	die(header('Location: index.php'));
}
elseif ($_POST['submit'])
{
	$title = trim(SqlEscape($_POST['title']));
	$text = trim(SqlEscape($_POST['text']));
	
	if ($title == '')
		$error = 'Your blog entry has no title. Enter a title and try again.';
	elseif ($text == '')
		$error = 'Your blog entry is empty. Enter some text and try again.';
	else
	{
		if ($new)
			SqlQuery("INSERT INTO blog_entries (userid, title, text, date) VALUES ({$myuserid}, '{$title}', '{$text}', UNIX_TIMESTAMP())");
		else
			SqlQuery("UPDATE blog_entries SET title='{$title}', text='{$text}' WHERE id={$id}");
			
		die(header('Location: index.php'));
	}
}
else
{
	$_POST['title'] = $entry['title'];
	$_POST['text'] = $entry['text'];
}

BuildHeader(array('title' => $actioncap.' blog entry'));

if ($error)
	MsgError($error);

?>
	<form action="" method="post">
		<table class="ptable">
			<tr>
				<th colspan=2><?php print $actioncap; ?> blog entry</th>
			</tr>
			<tr>
				<td class="c1 center bold" style="width: 150px;">Title:</td>
				<td class="c2 left"><input type="text" name="title" style="width: 100%;" maxlength=512 value="<?php print htmlspecialchars($_POST['title']); ?>"></td>
			</tr>
			<tr>
				<td class="c1 center bold">Text:</td>
				<td class="c2 left"><textarea name="text" style="width: 100%; height: 200px;"><?php print htmlspecialchars($_POST['text']); ?></textarea></td>
			</tr>
			<tr>
				<td class="c1">&nbsp;</td>
				<td class="c2 left"><input type="submit" name="submit" value="Submit"></td>
			</tr>
		</table>
	</form>
<?php

BuildFooter();

?>