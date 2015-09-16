<?php 

$html->script(
	array('vendors/jstree/jquery.jstree.js', 'vendors/tiny_mce/tiny_mce.js'),
	array('inline' => false)
); 

?>

<script type="text/javascript">

tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    convert_urls : "specific_textareas ",
	editor_selector: "tinymce",
	width: 560,
	height: 240,
	plugins: "paste",
	theme_advanced_buttons1: "bold,italic,underline,strikethrough,separator,separator,pastetext,pasteword,separator,link,unlink",
	theme_advanced_buttons2: "",
	theme_advanced_buttons3: ""
});

</script>


<div id="admin-content">
	
	<div id="side-col">
		<ul>
			<li><a href="/admin/news" class="icon-link back-link">Back to List</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('Article', array('url' => '/admin/news/save', 'id' => 'product-form', 'type' => 'file')); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['Article']['name'])): ?>
					<a href="/admin/news/delete/<?php echo intval($this->data['Article']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['Article']['name']); ?></h1>
				<?php else: ?>
					<h1>New Article</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>General</span></div>
				<div class="fieldset-box">
					<fieldset>
						
						<?php
						echo $form->input('id');
						echo $form->input('published');
						echo $form->input('name');
						echo $form->input('blurb', array(
							'style' => 'width: 560px; height: 80px;'
						));
						echo $form->input('content', array(
							'class' => 'tinymce'
						));
						?>
						
						<?php if (!empty($this->data['Article']['web_path'])): ?>
							
							<div class="input">
								<label for="void">File</label>
								<div class="image-preview">									
									<div><img src="<?php echo $this->data['Article']['web_path']; ?>" /></div>
									<div><a href="/admin/news/delete_image/<?php echo $this->data['Article']['id']; ?>">Delete image</a></div>
								</div>
							</div>
							
						<?php endif; ?>

						<?php
						$action = (!empty($this->data['Article']['ext1'])) ? 'Replace' : 'Upload';
						echo $form->input('file', array(
							'type' => 'file',
							'label' => $action . ' File'
						));
						?>

					</fieldset>
				</div>				
			</div>
			
			<div class="fieldset-box">
				<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


