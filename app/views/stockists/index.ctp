<?php

$this->set('title_for_layout', 'Find a Stockist');

// Don't show the "Free Delivery" banner on these pages
$this->set('show_delivery_banner', false);

?>
<div id="content" class="wide">
	<?php echo $this->element('template/static-box'); ?>

	<div id="static-text">
	
		<h1>Find a Stockist</h1>

		<div id="stock-page"><div id="support">
			<img src="/img/static-content-divider.gif" alt="" style="margin-top:10px">

			<div id="region-box">
				<div id="region-input">
					<?php
						echo $this->Form->create('Stockist', array('url' => $this->here));

						echo $this->Form->input('location', array(
							'options' => array(
								1 => 'United Kingdom',
								2 => 'Republic of Ireland',
								3 => 'Channel Islands',
							),
							'label' => 'Your Location',
						));
					?>
						<div id="location_1" class="location">
							<?php echo $this->Form->input('postcode', array(
								'label' => 'Postcode',
								'placeholder' => 'e.g. CM16 7FW',
							)); ?>
						</div>

						<div id="location_2" class="location">
							<?php echo $this->Form->input('county_2', array(
								'label' => 'County',
								'options' => $counties_1,
								'empty' => 'Please select...',
							)); ?>
						</div>

						<div id="location_3" class="location">
							<?php echo $this->Form->input('county_3', array(
								'label' => 'County',
								'options' => $counties_2,
								'empty' => 'Please select...',
							)); ?>
						</div>
					<?php
						echo $this->Form->submit('buttons/find.gif');

						echo $this->Form->end();
					?>
					<script>
					function changeLocation(){
						$('.location').hide();
						$('#location_' + $('#StockistLocation').val()).show();
					}
					$(document).ready(function(){
						$('#StockistLocation').change(changeLocation);
						changeLocation();
					});
					</script>
				</div>
			</div>

			<?php if(!empty($stockists)): ?>
			<table id="table-stockists" cellpadding="0" cellspacing="0" class="table-stockists">
				<thead>
					<tr class="header-shadow">
						<th class="retailer upper"><h2>Retailer</h2></th>
						<th class="address upper"><h2>Address</h2></th>
						<th class="contact upper"><h2>Contact</h2></th>
						<th class="hours upper"><h2>Opening Times</h2></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($stockists as $stockist): ?>
					<tr class="box-border">
						<td class="retailer">
							<span><?php echo $stockist['Stockist']['name']; ?></span><br/>
							<?php echo $stockist['Stockist']['town']; ?>
						</td>
						<td class="address">
							<?php echo $stockist['Stockist']['address_1']; ?><br/>
							<?php if(!empty($stockist['Stockist']['address_2'])): ?><?php echo $stockist['Stockist']['address_2']; ?><br/><?php endif; ?>
							<?php if(!empty($stockist['Stockist']['address_3'])): ?><?php echo $stockist['Stockist']['address_3']; ?><br/><?php endif; ?>
							<?php echo $stockist['Stockist']['town']; ?><br/>
							<?php echo $stockist['Stockist']['county']; ?><br/>
							<?php echo $stockist['Stockist']['postcode']; ?><br/><br/>
							<?php echo $this->Html->link('View Map', 'http://maps.google.co.uk/maps?q=' . $stockist['Stockist']['latitude'] . ',' . $stockist['Stockist']['longitude']); ?>
						</td>
						<td class="contact">
							<?php if(!empty($stockist['Stockist']['telephone'])): ?>
								<span>Telephone</span>
								<p><?php echo $stockist['Stockist']['telephone']; ?></p>
							<?php endif; ?>
							<?php if(!empty($stockist['Stockist']['email'])): ?>
								<span>Email</span>
								<p><?php echo $this->Html->link($stockist['Stockist']['email'], 'mailto:' . $stockist['Stockist']['email']); ?></p>
							<?php endif; ?>
							<?php if(!empty($stockist['Stockist']['website'])): ?>
								<span>Website</span>
								<p><?php echo $this->Html->link($stockist['Stockist']['website'], 'http://' . $stockist['Stockist']['website']); ?></p>
							<?php endif; ?>
						</td>
						<td class="hours">
							<dl>
								<dt>Mon</dt>
								<dd><?php echo $stockist['Stockist']['monday']; ?></dd>
								
								<dt>Tue</dt>
								<dd><?php echo $stockist['Stockist']['tuesday']; ?></dd>
								
								<dt>Wed</dt>
								<dd><?php echo $stockist['Stockist']['wednesday']; ?></dd>
								
								<dt>Thu</dt>
								<dd><?php echo $stockist['Stockist']['thursday']; ?></dd>
								
								<dt>Fri</dt>
								<dd><?php echo $stockist['Stockist']['friday']; ?></dd>
								
								<dt>Sat</dt>
								<dd><?php echo $stockist['Stockist']['saturday']; ?></dd>
								
								<dt>Sun</dt>
								<dd><?php echo $stockist['Stockist']['sunday']; ?></dd>
							</dl>
						</td>
					</tr>
					<tr><td colspan="5">&nbsp;</td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php elseif(isset($stockists) && $stockists == false): ?>
			<p>We were unable to find your postcode. Please try another.</p>
			<?php endif; ?>

		</div></div>

	</div>

	<div id="static-footer">
		<img src="/img/static-content-divider.gif" alt="" style="margin:5px 0px">
		<p style="text-align:right;">
		<a href="#">Back to Top</a>
		</p>
	</div>
</div>