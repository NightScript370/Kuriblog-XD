<?php

require('lib/common.php');

$id = (int)$_GET['id'];

if ($_GET['action'] == 'delete')
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
	Kill('You must be logged in to '.$action.' comments.');
	
if ($mypower < 0)
	Kill('You aren\'t allowed to '.$action.' comments.');

$comment = SqlQueryFetchRow("SELECT * FROM blog_comments WHERE id={$id}");
if (!$comment)
	Kill('Invalid blog entry ID.');
	
if (($mypower < 3) && ($entry['userid'] != $myuserid))
	Kill('You aren\'t allowed to '.$action.' this comment.');
	
$error = '';

if ($_GET['action'] == 'delete')
{
	SqlQuery("DELETE FROM blog_comments WHERE id={$id}");
	
	SqlQuery("UPDATE blog_entries SET ncomments=ncomments-1, lastcmtid=(SELECT MAX(id) FROM blog_comments WHERE entryid={$comment['entryid']}) WHERE id={$comment['entryid']}");
	SqlQuery("UPDATE blog_entries SET lastcmtuser=(SELECT userid FROM blog_comments WHERE id=lastcmtid) WHERE id={$comment['entryid']}");
	
	die(header('Location: comments.php?id='.$comment['entryid']));
}
elseif ($_POST['submit'])
{
	$text = trim(SqlEscape($_POST['text']));
	
	if ($text == '')
		$error = 'Your comment is empty. Enter some text and try again.';
	else
	{
		SqlQuery("UPDATE blog_comments SET text='{$text}' WHERE id={$id}");
			
		die(header('Location: comments.php?id='.$comment['entryid'].'&cid='.$id));
	}
}
else
{
	$_POST['text'] = $comment['text'];
}

BuildHeader(array('title' => $actioncap.' comment'));

if ($error)
	MsgError($error);

?>
	<form action="" method="post">
		<table class="ptable">
			<tr>
				<th colspan=2><?php print $actioncap; ?> comment</th>
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