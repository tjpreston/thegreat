
<div id="admin-content">
		
	<div id="side-col">
		
		<h3 id="exports">Generate Exports</h3>

		<ul id="admin-links">
			<li><a href="/admin/export/google_sitemap" class="icon-link google-link">Google Sitemap</a></li>
		</ul>
		
	</div>


	<div class="panes">
			
			<?php echo $session->flash(); ?>

			<?php if (!empty($path)): ?>
		
				<iframe 
					id="export-frame" 
					frameborder="0" 
					name="export-output" 
					scrolling="auto" 
					src="http://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo $path; ?>">
				</iframe>

			<?php endif; ?>


	</div>

</div>

