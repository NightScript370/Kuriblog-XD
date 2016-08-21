	<table class="ptable" id="pagefooter">
		<tr>
			<td class="c1 left vmiddle">
				<span style="float: left; margin-right: 5px;"><a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="http://www.w3.org/Icons/valid-html401" alt="Valid HTML 4.01 Transitional" height="31" width="88" title="this is HTML5 but who cares"></a></span>
				<span style="float: right; text-align: right;">Page rendered in <?php printf('%01.3f', $rendertime); ?> seconds.<br>MySQL: <?php print $nqueries; ?> queries, <?php print $nrowsf.'/'.$nrowst; ?> rows.</span>
				Powered by Kuriblog v1.1<br>
				copyright 2010-<?php print date('Y'); ?> Mega-Mario
			</td>
		</tr>
	</table>
</div>
</body>
</html>