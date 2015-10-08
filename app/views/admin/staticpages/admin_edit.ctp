
<div id="header">
     
    <h1><?php echo 'Edit ' . h($pagetitle); ?></h1>
</div>


<?php 
    $editstaticpageurl = strtolower($pagetitle);
    $editstaticpageurl = str_replace(' ', '-', $editstaticpageurl);
    echo $this->element('staticpages/edit-' . $editstaticpageurl); 
?>


