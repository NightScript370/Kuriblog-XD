<?php

header('Content-type: application/rss+xml');
require('lib/common.php');

$port = $_SERVER['SERVER_PORT'];
if ($port != ($_SERVER['HTTPS']?443:80)) $port = ':'.$port;
else $port = '';

$siteurl = 'http'.($_SERVER['HTTPS']?'s':'').'://'.$_SERVER['SERVER_NAME'].$port.preg_replace('{/[^/]*$}', '', $_SERVER['SCRIPT_NAME']);

print '<?xml version="1.0" encoding="UTF-8"?>';

?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title><?php echo SITE_TITLE; ?> RSS</title>
		<link><?php echo $siteurl; ?></link>
		<description>The latest news on <?php echo SITE_TITLE; ?>.</description>
		<atom:link href="<?php echo $siteurl; ?>/rss.php" rel="self" type="application/rss+xml" />

<?php
	$entries = SqlQuery("SELECT be.*, u.name uname FROM blog_entries be LEFT JOIN users u ON u.id=be.userid ORDER BY date DESC LIMIT 10");
	while($entry = SqlFetchRow($entries))
	{
		$title = htmlspecialchars($entry['title']);
		$timestamp = DateTime($entry['date']);
		$username = htmlspecialchars($entry['uname']);
		$text = Filter_BlogEntry($entry['text']);
		$rfcdate = gmdate(DATE_RFC1123, $entry['date']);
		
		print "\t\t<item>\n";
		print "\t\t\t<title>{$title} (posted on {$timestamp} by {$username}</title>\n";
		print "\t\t\t<link>{$siteurl}/?eid={$entry['id']}</link>\n";
		print "\t\t\t<pubDate>{$rfcdate}</pubDate>\n";
		print "\t\t\t<description><![CDATA[{$text}]]></description>\n";
		print "\t\t\t<guid isPermaLink=\"false\">e{$entry['id']}</guid>\n";
		print "\t\t</item>\n";
	}
?>
	</channel>
</rss>
