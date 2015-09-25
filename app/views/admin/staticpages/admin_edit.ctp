
<div id="header">
     
    <h1><?php echo 'Edit ' . h($pagetitle); ?></h1>
</div>

<?php
    echo $this->Form->create('Staticpage', array('action' => 'edit')); 
    echo $this->Form->input('header_1', array('div' => 'staticpageedit'));
    echo $this->Form->input('header_2', array('div' => 'staticpageedit'));
    echo $this->Form->input('header_3', array('div' => 'staticpageedit'));
    echo $this->Form->input('description_1', array('div' => 'staticpageedit'));
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->end('Save');
?>

  

