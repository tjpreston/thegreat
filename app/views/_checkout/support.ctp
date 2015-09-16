<?php
	if(!empty($basket['Basket']['stockist'])){
		$selectedStockist = $basket['Basket']['stockist'];
	} else {
		$selectedStockist = 0;
	}

	if(empty($chooseCounty)){
		$chooseCounty = false;
	}

	echo $this->Html->css(array(
		'checkout',
	), null, array('inline' => false));
?>

<div id="leftcol">
	<?php echo $this->element('template/customer_services'); ?>
</div>

<div id="content">
	<?php echo $this->element('basket/progress', array('selected' => 'support')); ?>
	
	<h1>Choose A Retailer For Aftersales Support</h1>
	<?php echo $this->element('template/breadcrumbs'); ?>

	<div class="checkout">
		<h4>Your Nearest Stockists</h4>
	</div>
	
	<?php echo $form->create('Basket', array('class' => 'form', 'url' => '/checkout/support')); ?>

	<div id="support">
		<div id="page-pad">
			<p id="text-top">This is to help should you need any further Sales, Warranty or After Sales support. <?php if(!$chooseCounty): ?>Based on your location we found following Michel Herbelin stockists near you.<?php endif; ?></p>

			<?php if($useCounty): ?>
			<p>Please choose a county to find stockists in: <?php
				echo $this->Form->input('Basket.stockist_county_id', array(
					'options' => $counties,
					'empty' => 'Please select...',
					'div' => false,
					'label' => false,
					'id' => 'county-select',
					'value' => $this->Session->read('Basket.stockist_county_id'),
				));
			?></p>
			<script>
			$(document).ready(function(){
				$('#county-select').change(function(){
					var myForm = $(this).parents('form');
					$('input[type=radio]', myForm).attr('checked', false);
					myForm.submit();
				})
			});
			</script>
			<?php endif; ?>

			<?php if(!empty($error)): ?>
			<div id="flashMessage" class="failure"><?php echo $error; ?></div>
			<?php endif; ?>
			
			<?php if(!empty($stockists)): ?>

			<table cellspacing="0" cellpadding="0" class="table-address">
				<tbody>
					<tr class="header-shadow">
						<th class="upper" width="170px"><h2>Retailer</h2></th>
						<th class="upper" width="160px"><h2>Address</h2></th>
						<th class="upper" width="200px"><h2>Contact</h2></th>
						<th>&nbsp;</th>
					</tr>

					<?php foreach($stockists as $stockist): ?>
					<tr class="box-border">
						<td class="top-box"><span><?php echo h($stockist['Stockist']['name']); ?></span><br>
						<?php echo h($stockist['Stockist']['town']); ?></td>
						<td class="top-box">
							<?php echo h($stockist['Stockist']['address_1']); ?><br>
							<?php if(!empty($stockist['Stockist']['address_2'])): ?><?php echo h($stockist['Stockist']['address_2']); ?><br/><?php endif; ?>
							<?php if(!empty($stockist['Stockist']['address_3'])): ?><?php echo h($stockist['Stockist']['address_3']); ?><br/><?php endif; ?>
							<?php echo h($stockist['Stockist']['town']); ?><br>
							<?php echo h($stockist['Stockist']['county']); ?><br>
							<?php echo h($stockist['Stockist']['postcode']); ?><br>
						</td>
						<td class="top-box"><?php

							$fields = array('telephone', 'email', 'website');
							foreach($fields as $field){
								if(!empty($stockist['Stockist'][$field])){
									echo '<p>' . h($stockist['Stockist'][$field]) . '</p>';
								}
							}

						?></td>
						<td> 
						<div class="make-box">
							<div class="make-option-lite">
								<input type="radio" name="data[Basket][stockist]" value="<?php echo $stockist['Stockist']['id']; ?>" id="radio<?php echo $stockist['Stockist']['id']; ?>"<?php if($stockist['Stockist']['id'] == $selectedStockist){ echo ' checked="checked"'; } ?> />
								<label for="radio<?php echo $stockist['Stockist']['id']; ?>">Make this<br/>my shop</label>
							</div>
						</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php elseif(isset($stockists) && $stockists == false): ?>
			<p>We were unable to find stockists in your location. Please try another.</p>
			<?php endif; ?>

			<div style="clear:both"></div>

			<div class="basket-continue">
				<?php echo $form->submit('/img/buttons/continue-secure.gif', array('div' => false, 'style' => 'float:right')); ?>
				<a href="/checkout"><img src="/img/buttons/back-details.gif" alt="Back to Details"></a>
			</div>

		</div>
	</div>

	<?php echo $form->end(); ?>
</div>