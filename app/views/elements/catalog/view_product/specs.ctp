<?php if(!empty($record['ProductDescription']['specification_array'])): ?>

<div class="techspecs">
	<h3>Technical Specs</h3>
	<table>
		<tbody>
			<?php foreach($record['ProductDescription']['specification_array'] as $spec): ?>
			<tr>
				<th style="vertical-align:top"><?php echo (!empty($spec[0]) ? h($spec[0]) . ':' : ''); ?></th>
				<td><?php echo h($spec[1]); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php endif; ?>