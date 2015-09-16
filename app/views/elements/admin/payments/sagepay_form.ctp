<div>
	<div class="fieldset-header"><span>Sagepay Form Response</span></div>
	<div class="fieldset-box">
		<dl>
			
			<dt>Status</dt>
			<dd><?php echo h($record['SagepayFormOrder']['status']); ?> (<?php echo h($record['SagepayFormOrder']['status_detail']); ?>)</dd>
			<dt>VSP ID</dt>
			<dd><?php echo h($record['SagepayFormOrder']['vsp_tx_id']); ?></dd>
			<dt>TX Auth. No.</dt>
			<dd><?php echo h($record['SagepayFormOrder']['tx_auth_no']); ?></dd>
			
			<dt>Address Check</dt>
			<dd><?php echo h($record['SagepayFormOrder']['address_result']); ?></dd>
			<dt>Postcode Check</dt>
			<dd><?php echo h($record['SagepayFormOrder']['postcode_result']); ?></dd>
			<dt>CV2 Check</dt>
			<dd><?php echo h($record['SagepayFormOrder']['cv2_result']); ?></dd>
			
			<dt>3D Secure Check</dt>
			<dd>
				<?php echo h($record['SagepayFormOrder']['3d_secure_status']); ?>
				<?php if (!empty($record['SagepayFormOrder']['cavv'])): ?>
					(<?php echo h($record['SagepayFormOrder']['cavv']); ?>)
				<?php endif; ?>
			</dd>
			
			<dt>Card</dt>
			<dd><?php echo h($record['SagepayFormOrder']['card_type']); ?> (ending <?php echo intval($record['SagepayFormOrder']['last_4_digits']); ?>)</dd>
			
		</dl>
	</div>
</div>

