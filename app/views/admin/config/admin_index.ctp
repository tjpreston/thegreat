<?php


$tabs = array(
	'home' 		=> array(0, 'Homepage')
);

$tabIndex = 0;
if (!empty($this->params['named']['tab']) && !empty($tabs[$this->params['named']['tab']]))
{
	$tabIndex = $tabs[$this->params['named']['tab']][0];
}
else if (!empty($initTab))
{
	$tabIndex = $tabs[$initTab][0];
}

?>

<script type="text/javascript">	

$(function() {
	
	$("ul#product-nav").tabs("div.panes > div.pane", {
		initialIndex: <?php echo intval($tabIndex); ?> 
	});
	
	var api = $("ul#product-nav").data("tabs");
	
	$("#product-form").submit(function() {
		var pane = $(api.getCurrentPane())
		var id = pane.attr("id").substring(5);
		$("#last-pane").val(id);
		return true;
	});
	
	$("ul.lang-nav").tabs("div.fieldsets > fieldset");	
	
});

</script>


<div id="admin-content">
	
	<div id="side-col">
		<p><a href="/admin/products" class="icon-link back-link">Back to List</a></p>
		<ul id="product-nav">
			<?php $i = 0; ?>
			<?php foreach ($tabs as $tab => $tabData): ?>
				<?php $error = (!empty($errorsOnTabs) && in_array($tab, $errorsOnTabs)) ? ' class="tab-error"' : ''; ?>
				<li<?php echo $error; ?>><a href="#"><?php echo $tabData[1]; ?></a></li>
				<?php $i++; ?>
			<?php endforeach; ?>
		</ul>
	</div>
	
	<?php echo $form->create('Config', array('url' => '/admin/config/save', 'id' => 'product-form')); ?>

	<div class="panes">
		
		<?php echo $session->flash(); ?>
				
		<div id="header">
			<h1>Site Configuration</h1>
		</div>

		<div id="pane-desc" class="pane">			
			<div class="fieldset-header">Homepage Meta</div>
			<div class="fieldset-box">
				<?php echo $this->element('admin/language_nav'); ?>
				<div class="fieldsets">

					<?php foreach ($languages as $languageID => $languageName): ?>
						<fieldset>		
														
							<?php
							
							echo $form->hidden('ConfigHomepage.' . $languageID . '.id');
							echo $form->hidden('ConfigHomepage.' . $languageID . '.config_id');
							echo $form->hidden('ConfigHomepage.' . $languageID . '.language_id', array('value' => $languageID));
							
							$title = (!empty($this->data['ConfigHomepage'][$languageID]['title'])) ? $this->data['ConfigHomepage'][$languageID]['title'] : '';
							echo $form->input('ConfigHomepage.' . $languageID . '.title', array('value' => $title));
							
							$keywords = (!empty($this->data['ConfigHomepage'][$languageID]['meta_keywords'])) ? $this->data['ConfigHomepage'][$languageID]['meta_keywords'] : '';
							echo $form->input('ConfigHomepage.' . $languageID . '.meta_keywords', array('value' => $keywords));
							
							$desc = (!empty($this->data['ConfigHomepage'][$languageID]['meta_description'])) ? $this->data['ConfigHomepage'][$languageID]['meta_description'] : '';
							echo $form->input('ConfigHomepage.' . $languageID . '.meta_description', array('value' => $desc));
							
							?>
							
						</fieldset>
					<?php endforeach; ?>
					
				</div>
			</div>
			<?php echo $form->submit('Save'); ?>
		</div>
		
	</div>
	
	<?php echo $form->end(); ?>
	
</div>




