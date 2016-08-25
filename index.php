<?php

require('lib/common.php');

BuildHeader(array('descr' => META_DESCR));

$epp = 10;
$numentries = SqlQueryResult("SELECT COUNT(*) FROM blog_entries");
if ($_GET['eid'])
{
	$eid = (int)$_GET['eid'];
	$numonpage = SqlQueryResult("SELECT COUNT(*) FROM blog_entries WHERE id>={$eid}");
	$_GET['p'] = ceil($numonpage / $epp);
}

$start = (PageNum() - 1) * $epp;
$entries = SqlQuery("	SELECT be.*, u.id uid, u.name uname, u.sex usex, u.powerlevel upowerlevel, u2.id u2id, u2.name u2name, u2.sex u2sex, u2.powerlevel u2powerlevel, c.guestname
						FROM blog_entries be 
							LEFT JOIN users u ON u.id=be.userid
							LEFT JOIN users u2 ON u2.id=be.lastcmtuser
							LEFT JOIN blog_comments c ON c.id=be.lastcmtid
						ORDER BY date DESC LIMIT {$start},{$epp}");

if (!$numentries) 
	Message('No blog entries posted.');
else
{
	print "\t".PageLinks($numentries, $epp);

	while ($entry = SqlFetchRow($entries))
	{
		$title = htmlspecialchars($entry['title']);
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
		
		if ($entry['lastcmtuser'])
			$lastlink = UserName($entry, 'u2');
		else
			$lastlink = htmlspecialchars($entry['guestname']);
			
		if ($entry['ncomments'] > 1)
			$cmtlink1 = "<a href=\"comments.php?id={$entry['id']}\">{$entry['ncomments']} comments</a> (last by ".$lastlink.")";
		else if ($entry['ncomments'] > 0)
			$cmtlink1 = "<a href=\"comments.php?id={$entry['id']}\">1 comment</a> (by ".$lastlink.")";
		else
			$cmtlink1 = "No comments yet";
			
		if ($login || GUESTCOMMENTS)
			$cmtlink2 = "<a href=\"comments.php?id={$entry['id']}&amp;last#post\">Post a comment</a>";
		else
			$cmtlink2 = "<a href=\"login.php\">Log in</a> to post a comment";
		
		print 
	"	<table class=\"ptable\">
			<tr>
				<th class=\"left vtop\">
					{$userlink}
				</th>
				<th class=\"left vtop\">
					<span style=\"float: right;\" class=\"nonbold\">{$adminopts}</span>
					{$title}
				</th>
			</tr>
			<tr>
				<td class=\"c1 padded left\">
					<span class=\"smaller nonbold\">Posted on {$timestamp}</span>
				</td>
				<td class=\"c1 padded left\">
					{$text}
				</td>
			</tr>
			<tr>
				<td class=\"c2 right\">
					{$cmtlink1} | {$cmtlink2}
				</td>
			</tr>
		</table>
	";
	}

	print "\t".PageLinks($numentries, $epp);
}

BuildFooter();

?>
