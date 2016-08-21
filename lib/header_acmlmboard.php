<!DOCTYPE html>
<html>
<head>
	<title><?php print $title; ?></title>
	<meta name="keywords" content="<?php print META_KEYWORDS; ?>">
<?php print $descr; ?>
	<link rel="stylesheet" href="theme/common.css" type="text/css">
	<link rel="stylesheet" href="theme/<?php print $themefile; ?>" type="text/css">
	<link rel="alternate" type="application/rss+xml" title="RSS feed" href="rss.php">
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<?php print $params['headextra']; ?>
</head>
<body>
<div class="maincontainer">
	<table class="ptable" id="pageheader">
		<tr>
			<td class="c1 center" colspan=3>
				<a href="./"><img src="<?php print $bannerimg; ?>" title="<?php print $bannertitle; ?>" alt="<?php print $banneralt; ?>"></a>
			</td>
		</tr>
		<tr>
			<td class="c2 center" rowspan=2 style="width: 15%;">
				<?php print $nviews; ?> views <!-- and <?php print $nbotviews; ?> by bots -->
			</td>
			<td class="c2 center">
				<a href="./">Main</a>
				| <a href="rss.php">RSS feed</a>
				<!-- add your own headerlinks here -->
			</td>
			<td class="c2 center" rowspan=2 style="width: 15%;">
				<?php print DateTime(); ?>
			</td>
		</tr>
		<tr>
			<td class="c1 center">
<?php if ($login) { ?>
				<?php print Username($myuserdata); ?>:
				<a href="login.php?logout">Log out</a>
				<?php if ($mypower >= 0) { ?>| <a href="editprofile.php">Edit profile</a><?php } ?>
				<?php if ($mypower >= 2) { ?>| <a href="newblogentry.php">New blog entry</a><?php } ?>
				<?php if ($mypower >= 3) { ?>| <a href="admin.php">Admin</a><?php } ?>
<?php } else { ?>
				Guest:
				<a href="register.php">Register</a>
				| <a href="login.php">Log in</a>
<?php } ?>
			</td>
		</tr>
	</table>
