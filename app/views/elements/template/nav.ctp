<div class="grid_24 header-nav">
	<nav id="sitenav" class="border-top-bottom">
		<ul class="clearfix">
			<?php foreach($categories as $category): ?>
				<?php
					if($category['Category']['display_on_main_nav'] == 0){
						continue;
					}

					if($category['Category']['enable_subcategory_dropdown'] && !empty($category['children'])){
						$hasDropdown = true;
					} else {
						$hasDropdown = false;
					}
				?>
				<li<?php if($hasDropdown) echo ' class="has-dropdown"'; ?> onClick="return true"><?php 
					$options = array();
					if($this->here == $category['CategoryName']['full_url']){
						$options['class'] = 'selected';
					} else {
						// Check to see if this category has a child which is the current page
						$hasActiveChild = false;
						if(!empty($category['children'])){
							foreach($category['children'] as $child){
								if($this->here == $child['CategoryName']['full_url']){
									$hasActiveChild = true;
								}
							}
						}

						if($hasActiveChild){
							$options['class'] = 'selected';
						}
					}

					$name = $category['CategoryName']['menu_name'];
					if(empty($name)){
						$name = $category['CategoryName']['name'];
					}

					echo $this->Html->link(
						$name,
						$category['CategoryName']['full_url'],
						$options
					);

					if($hasDropdown){
						echo '<ul>';

						foreach($category['children'] as $child){
							echo '<li>';

							$name = $child['CategoryName']['menu_name'];
							if(empty($name)){
								$name = $child['CategoryName']['name'];
							}
                                                        if($name == 'CHRISTMAS')
                                                        {
                                                            echo '<a href="/home/christmas" style="background-color: red;">CHRISTMAS</a>';
                                                        } 
                                                        else
                                                        {
                                                        echo $this->Html->link(
								$name,
								$child['CategoryName']['full_url']
							);
                                                        }
							echo '</li>';
						}

						echo '</ul>';
					}

				?></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>
