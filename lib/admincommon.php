<?php

require('common.php');

if ($mypower < 3)
	Kill("This function is reserved to administrators.");


function BuildAdminBar($page)
{
	$pages = array(	'admin' => 'General settings',
					'ipbans' => 'IP bans');
					
	$adminfuncs = '';
	foreach ($pages as $p=>$d)
	{
		if ($adminfuncs) $adminfuncs .= ' | ';
		$adminfuncs .= ($p==$page) ? $d : "<a href=\"{$p}.php\">{$d}</a>";
	}
	
	print 
"	<table class=\"ptable\">
		<tr>
			<th>Admin functions</th>
		</tr>
		<tr>
			<td class=\"c1 center padded\">
				{$adminfuncs}
			</td>
		</tr>
	</table>
";
}

?>