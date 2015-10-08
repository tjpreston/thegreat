<?php
    echo $this->Form->create('Staticpage', array('action' => 'edit')); 
    echo $this->Form->input('header_1', array('div' => 'staticpageedit'));
    echo $this->Form->input('header_2', array('div' => 'staticpageedit'));
    echo $this->Form->input('header_3', array('div' => 'staticpageedit'));
    echo $this->Form->input('description_1', array('div' => 'staticpageedit'));
    
    echo $this->Form->input('category_text_1', array('div' => 'staticpageedit'));
    echo $this->Form->input('category_text_2', array('div' => 'staticpageedit'));
    echo $this->Form->input('category_text_3', array('div' => 'staticpageedit'));
    echo $this->Form->input('category_text_4', array('div' => 'staticpageedit'));
    
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->end('Save');
?>

<br>
<div id="header">
     
     <h1>Images</h1>
</div>  
