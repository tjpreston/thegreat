<div id="pane-pricing" class="pane">

	<div class="fieldset-header">
		<span>Price</span>
	</div>

	<div class="fieldset-box">
		<?php // echo $this->element('admin/currency_nav'); ?>
		<div class="fieldsets">
			
			<?php foreach ($currencies as $c => $currency): ?>
				
				<?php $currencyID = $currency['Currency']['id']; ?>
				
				<fieldset>
					
					<?php 					
					echo $form->input('ProductPrice.' . $currencyID . '.id');					
					echo $form->hidden('ProductPrice.' . $currencyID . '.currency_id', array(
						'value' => $currencyID
					));

					$costPrice = (!empty($this->data['ProductPrice'][$currencyID]['cost_price'])) ? $this->data['ProductPrice'][$currencyID]['cost_price'] : '';
					echo $form->input('ProductPrice.' . $currencyID . '.cost_price', array(
						'class' => 'smallest currency-input currency-input-' . intval($currencyID),
						'label' => 'Cost Price',
						'value' => $costPrice
					));
					
					$basePrice = (!empty($this->data['ProductPrice'][$currencyID]['base_price'])) ? $this->data['ProductPrice'][$currencyID]['base_price'] : '';
					echo $form->input('ProductPrice.' . $currencyID . '.base_price', array(
						'class' => 'smallest currency-input currency-input-' . intval($currencyID),
						'label' => 'List Price',
						'value' => $basePrice
					));


					?>
					
					<?php if (Configure::read('Admin.pricing.show_rrp')): ?>
						<?php
						$baseRrp = (!empty($this->data['ProductPrice'][$currencyID]['base_rrp'])) ? $this->data['ProductPrice'][$currencyID]['base_rrp'] : '';
						echo $form->input('ProductPrice.' . $currencyID . '.base_rrp', array(
							'class' => 'smallest currency-input currency-input-' . intval($currencyID),
							'label' => 'RRP',
							'value' => $baseRrp
						));						
						?>					
					<?php endif; ?>
					
					<?php if (Configure::read('Admin.pricing.show_wholesale')): ?>				
						<?php
						$wholesale = (!empty($this->data['ProductPrice'][$currencyID]['wholesale'])) ? $this->data['ProductPrice'][$currencyID]['wholesale'] : '';
						echo $form->input('ProductPrice.' . $currencyID . '.wholesale', array(
							'class' => 'smallest currency-input currency-input-' . intval($currencyID),
							'label' => 'Wholesale',
							'value' => $wholesale
						));
						?>
					<?php endif; ?>
					
					<?php if (Configure::read('Admin.pricing.show_special')): ?>					
						<?php
						$specialPrice = (!empty($this->data['ProductPrice'][$currencyID]['special_price'])) ? $this->data['ProductPrice'][$currencyID]['special_price'] : '';
						echo $form->input('ProductPrice.' . $currencyID . '.special_price', array(
							'class' => 'smallest currency-input currency-input-' . intval($currencyID),
							'value' => $specialPrice
						));
						?>	
						<?php /*
						<div class="input text multiple">
							<p>Special Price Available</p>
							<?php
							foreach (array('from', 'to') as $dir)
							{
								echo $this->element('admin/date_input', array(
									'dir' => $dir,
									'value' => isset($this->data['ProductPrice']['special_price_date_' . $dir]) ? $this->data['ProductPrice']['special_price_date_' . $dir] : '',
									'field' => 'special_price_date_' . $currencyID . '_' . $dir,
									'input' => '[ProductPrice][' . $currencyID . '][special_price_date_' . $dir . ']'
								));
							}
							?>
						</div>
						*/?>
					<?php endif; ?>

					<?php /*
					$tradePrice = (!empty($this->data['ProductPrice'][$currencyID]['trade_price'])) ? $this->data['ProductPrice'][$currencyID]['trade_price'] : '';
					echo $form->input('ProductPrice.' . $currencyID . '.trade_price', array(
						'class' => 'smallest currency-input currency-input-' . intval($currencyID),
						'label' => 'Trade Price',
						'value' => $tradePrice
					));*/
					?>

				</fieldset>
				
			<?php endforeach; ?>
		</div>
	</div>
	
	<?php if (Configure::read('Catalog.use_tiered_customer_pricing')): ?>
	
		<?php echo $this->element('admin/products/customer_discount_price_matrix', array(
			'fkID' => $this->data['Product']['id'],
			'model' => 'ProductPriceDiscount',
			'fk' => 'product_id'
		)); ?>
	
	<?php endif; ?>

</div>




