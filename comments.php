<?php

require('lib/common.php');

$entryid = (int)$_GET['id'];
$entry = SqlQueryFetchRow("	SELECT be.*, u.id uid, u.name uname, u.sex usex, u.powerlevel upowerlevel 
							FROM blog_entries be 
								LEFT JOIN users u ON u.id=be.userid 
							WHERE be.id={$entryid}");
							
if (!$entry)
	Kill('Invalid blog entry ID.');

$error = '';
if ($_POST['postcomment'])
{
	if (!$login)
	{
		if (!GUESTCOMMENTS)
			$error = 'You must be logged in to post comments.';
		else if (!trim($_POST['name']))
			$error = 'You must enter a name.';
		else if (SqlQueryResult("SELECT COUNT(*) FROM users WHERE name='".SqlEscape(trim($_POST['name']))."'"))
			$error = 'This name is already taken by a registered user.';
	}
	
	if (!$error)
	{
		if ($mypower < 0)
			$error = 'Nice try, kid, but no. You\'re still banned!';
		else
		{
			if ($mypower >= 3)
				$lastcomments = 0;
			else
				$lastcomments = SqlQueryResult("SELECT COUNT(*) FROM blog_comments WHERE userid={$myuserid} AND date>=".(time()-86400));
			
			if ($lastcomments >= 20)
				$error = 'You posted enough comments for today. Come back tomorrow.';
			else if (trim($_POST['text']) == '')
				$error = 'Your comment is empty. Enter some text and try again.';
			else
			{
				$text = SqlEscape($_POST['text']);
				$date = time();
				
				if (!$login)
					$guestname = SqlEscape(trim($_POST['name']));
				
				SqlQuery("INSERT INTO blog_comments (entryid, userid, guestname, text, date, ip) VALUES ({$entryid}, {$myuserid}, '{$guestname}', '{$text}', {$date}, '".SqlEscape($_SERVER['REMOTE_ADDR'])."')");
				SqlQuery("UPDATE blog_entries SET ncomments=ncomments+1, lastcmtid=LAST_INSERT_ID(), lastcmtuser={$myuserid} WHERE id={$entryid}");
			}
		}
	}
	
	if (!$error)
		die(header('Location: comments.php?id='.$entryid.'&last'));
}

$title = htmlspecialchars($entry['title']);
BuildHeader(array('title' => $title));

$crumbs = BuildCrumbs(array('./'=>'Main', 'lol'=>"{$title} &raquo; Comments"));
print $crumbs;

{
	$userlink = UserName($entry, 'u');
	$text = Filter_BlogEntry($entry['text']);
	$timestamp = DateTime($entry['date']);
	
	$adminopts = '';
	if (($mypower >= 3) || (($mypower >= 2) && ($entry['userid'] == $myuserid)))
	{
		$adminopts .= "<a href=\"editblogentry.php?id={$entry['id']}\">Edit</a>";
		$adminopts .= " | <a href=\"editblogentry.php?action=delete&amp;id={$entry['id']}&amp;token={$mytoken}\" 
			onclick=\"if (!confirm('Really delete this blog entry?')) return false;\">Delete</a>";
	}
	
	print "
	<table class=\"ptable\">
		<tr>
			<th class=\"left vtop\">
				<span style=\"float: right;\" class=\"nonbold\">{$adminopts}</span>
				{$title}<br>
				<span class=\"smaller nonbold\">Posted on {$timestamp} by {$userlink}</span>
			</th>
		</tr>
		<tr>
			<td class=\"c1 padded left\">
				{$text}
			</td>
		</tr>
	</table>
";
}

$cpp = 20;
$ncomments = SqlQueryResult("SELECT COUNT(*) FROM blog_comments WHERE entryid={$entryid}");
if (isset($_GET['last']))
	$_GET['p'] = ceil($ncomments / $cpp);
else if ($_GET['cid'])
{
	$cid = (int)$_GET['cid'];
	$numonpage = SqlQueryResult("SELECT COUNT(*) FROM blog_comments WHERE entryid={$entryid} AND id<={$cid}");
	$_GET['p'] = ceil($numonpage / $cpp);
}

$start = (PageNum() - 1) * $cpp;
$comments = SqlQuery("	SELECT bc.*, u.id uid, u.name uname, u.sex usex, u.powerlevel upowerlevel 
						FROM blog_comments bc
							LEFT JOIN users u ON u.id=bc.userid
						WHERE bc.entryid={$entryid}
						ORDER BY date LIMIT {$start},{$cpp}");

if ($ncomments > 1)
	$ncmtstr = "{$ncomments} comments have been posted.";
else if ($ncomments > 0)
	$ncmtstr = "1 comment has been posted.";
else
	$ncmtstr = "No comments have been posted yet.";

print "\t<table class=\"ptable\"><tr><td class=\"c1 left\">{$ncmtstr}</td></tr></table>\n";

print "\t".PageLinks($ncomments, $cpp);

while ($comment = SqlFetchRow($comments))
{
	if ($comment['userid'])
		$userlink = UserName($comment, 'u');
	else
		$userlink = '<strong>'.htmlspecialchars($comment['guestname']).'</strong>';
	
	$text = Filter_BlogComment($comment['text']);
	$timestamp = DateTime($comment['date']);
	
	$adminopts = '';
	if ($mypower >= 1 || ($login && $comment['userid'] == $myuserid))
	{
		$adminopts .= "<a href=\"editcomment.php?id={$comment['id']}\">Edit</a>";
		$adminopts .= " | <a href=\"editcomment.php?action=delete&amp;id={$comment['id']}&amp;token={$mytoken}\" 
				onclick=\"if (!confirm('Really delete this comment?')) return false;\">Delete</a>";
	}
	if ($mypower >= 3)
		$adminopts .= " | {$comment['ip']}";
	
	print "
	<table class=\"ptable\">
		<tr>
			<th class=\"left vtop\">
				<span style=\"float: right;\" class=\"nonbold\">{$adminopts}</span>
				{$userlink} says:<br>
				<span class=\"smaller nonbold\">Posted on {$timestamp}</span>
			</th>
		</tr>
		<tr>
			<td class=\"c1 padded left\">
				{$text}
			</td>
		</tr>
	</table>
";
}

print "\t".PageLinks($ncomments, $cpp);

if ($login || GUESTCOMMENTS)
{
	if ($mypower < 0)
		print "\t<table class=\"ptable\"><tr><td class=\"c1 left\">Banned users may not post comments.</td></tr></table>\n";
	else
	{
		if ($error)
			MsgError($error);
?>
	<form action="" method="post" id="post">
		<table class="ptable">
			<tr>
				<th>Post a comment</th>
			</tr>
			<tr>
				<td class="c2 left">
					<?php if (!$login) { ?>
					Name: <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name']); ?>" size="20" maxlength="20"><br>
					<?php } ?>
					<textarea name="text" wrap="virtual" style="width: 100%; height: 200px;"></textarea>
				</td>
			</tr>
			<tr>
				<td class="c2 left"><input type="submit" name="postcomment" value="Post comment"></td>
			</tr>
		</table>
	</form>
<?php
	}
}
else
	print "\t<table class=\"ptable\"><tr><td class=\"c1 left\"><a href=\"login.php\">Log in</a> to post a comment.</td></tr></table>\n";
	
print $crumbs;

BuildFooter();

?>