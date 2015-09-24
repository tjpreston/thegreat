
<div id="header">
     
    <h1><?php echo 'Edit ' . h($pagetitle); ?></h1>
</div>

<?php
    echo $this->Form->create('Staticpage');
    echo $this->Form->input('name');
    echo $this->Form->input('body', array('rows' => '3'));
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->end('Save');
?>

  
<!--      
<?php echo $this->Form->create('Staticpage', array('action' => 'edit')); ?>
echo $this->Form->input('description_1');
      echo $this->Form->input('id', array('type' => 'hidden'));
      echo $this->Form->end('Finish'); ?>-->

<!--    echo $this->Form->input('title');
    echo $this->Form->input('body', array('rows' => '3'));
    echo $this->Form->input('id', array('type' => 'hidden'));-->

