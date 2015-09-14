<?php

$this->set('title_for_layout', 'Request a Catalogue');

// Don't show the "Free Delivery" banner on these pages
$this->set('show_delivery_banner', false);

?>
<div id="content" class="wide">
	<?php echo $this->element('template/static-box'); ?>

	<div id="static-text">

		<h1>Request a Catalogue</h1>
		<img src="/img/static-content-divider.gif" alt="" style="margin-top:10px" />

		<?php echo $this->Session->flash(); ?>

		<p class="headline" style="padding-bottom: 0">Please send me a free copy of your colour brochure and a list of official stockists in my area. My details are as follows:</p>

		<?php echo $this->Form->create('Form', array('url' => $this->here)); ?>

		<?php

		$options = array(
			'label' => false,
			'div' => false,
		);

		$errorOptions = array(
			'escape' => false,
			'wrap' => false,
		);
		?>

		<table class="catalogue" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr class="mandatory">
					<th>Title</th>
					<td<?php echo $form->error('title', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('title', am($options, array(
							'options' => array(
								'Mr' => 'Mr',
								'Mrs' => 'Mrs',
								'Miss' => 'Miss',
								'Ms' => 'Ms',
								'Dr' => 'Dr',
							),
							'empty' => 'Please select...',
						)));
					?></td>
				</tr>
				<tr class="mandatory">
					<th>First Name</th>
					<td<?php echo $form->error('first_name', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('first_name', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>Surname</th>
					<td<?php echo $form->error('surname', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('surname', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>Email</th>
					<td<?php echo $form->error('email', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('email', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>Telephone</th>
					<td<?php echo $form->error('telephone', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('telephone', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>Address</th>
					<td<?php echo $form->error('address_line_1', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('address_line_1', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>&nbsp;</th>
					<td<?php echo $form->error('address_line_2', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('address_line_2', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>&nbsp;</th>
					<td<?php echo $form->error('address_line_3', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('address_line_3', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>Town</th>
					<td<?php echo $form->error('town', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('town', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>County</th>
					<td<?php echo $form->error('county', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('county', $options);
					?></td>
				</tr>
				<tr class="mandatory">
					<th>Postcode</th>
					<td<?php echo $form->error('postcode', ' class="error"', $errorOptions); ?>><?php
						echo $this->Form->input('postcode', $options);
					?></td>
				</tr>
			</tbody>
		</table>

		<div id="static-footer">
          <img src="/img/static-content-divider.gif" alt="" style="margin:5px 0px">
          <p style="text-align:right;">
            <?php echo $this->Form->submit('buttons/send-request.gif', array('div' => false)); ?>
          </p>
        </div>

        <?php echo $this->Form->end(); ?>

	</div>
</div>