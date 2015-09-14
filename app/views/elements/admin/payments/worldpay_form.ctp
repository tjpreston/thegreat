<div>
	<div class="fieldset-header"><span>Worldpay Form Response</span></div>
	<div class="fieldset-box">
		<dl>
			<?php //debug($record); ?>
			<dt>Status</dt>
			<?php if($record['WorldpayFormOrder']['transStatus'] == 'Y'):?>
				<dd>Successful</dd>
			<?php elseif($record['WorldpayFormOrder']['transStatus'] == 'C') : ?>
				<dd>Cancelled</dd>
			<?php endif; ?>

		
			<dt>Auth Mode</dt>
			<?php if($record['WorldpayFormOrder']['authMode'] == 'A'):?>
				<dd>Full Auth</dd>
			<?php elseif($record['WorldpayFormOrder']['authMode'] == 'E') : ?>
				<dd>Pre Auth</dd>
			<?php endif; ?>
			
			<dt>Country Match</dt>
			<?php if($record['WorldpayFormOrder']['countryMatch'] == 'Y'):?>
				<dd>Matched</dd>
			<?php elseif($record['WorldpayFormOrder']['countryMatch'] == 'N') : ?>
				<dd>No Match</dd>
			<?php elseif($record['WorldpayFormOrder']['countryMatch'] == 'B') : ?>
				<dd>Comparison Not Available</dd>
			<?php elseif($record['WorldpayFormOrder']['countryMatch'] == 'I') : ?>
				<dd>Contact Country Not Supplied</dd>
			<?php elseif($record['WorldpayFormOrder']['countryMatch'] == 'S') : ?>
				<dd>Card Issue Country Not Available</dd>
			<?php endif; ?>
			
			

			<dt>Card</dt>
			<dd><?php echo h($record['WorldpayFormOrder']['cardType']); ?></dd>

			<dt>IP Address</dt>
			<dd><?php echo h($record['WorldpayFormOrder']['ipAddress']); ?></dd>
		</dl>


		<table id="worldpay">
				<tr>
					<th class="heading" colspan="4">internal fraud-related checks</th>
				</tr>
				<tr>
					<th>Card Verification Value check</th>
					<th>postcode AVS check</th>
					<th>address AVS check</th>
					<th>country comparison check</th>
				</tr>
				<tr>

				<?php
					for($i = 0; $i < 4; $i++){
						echo '<td>';
							$avs = substr($record['WorldpayFormOrder']['AVS'], $i, 1);
							if($avs == 0):
								echo 'Not supported';
							elseif($avs == 1):
								echo 'Not checked';
							elseif($avs == 2):
								echo 'Matched';
							elseif($avs == 4):
								echo 'Not matched';
							elseif($avs == 8):
								echo 'Partially matched';
							endif;
						echo '</td>';
					}
				?>
				</tr>
			</table>
	</div>
</div>

