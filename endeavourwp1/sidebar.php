 <?php
	// static $first_dropdown = true;
	$current_user = wp_get_current_user();
	$user_name = "";
	if ( ( $current_user instanceof WP_User ) ) {
		$logedin = $current_user->exists();
		$user_name = esc_html( $current_user->display_name );
	}
?>
 <div class="col-sm-2 blog-sidebar m-0 p-2">
	<div class="m-0 p-2">
	<?php if ($logedin) {
		echo "<strong>" . $user_name . "</strong> (<a href='" . wp_logout_url() . "'>Logout</a>)";
	} else { ?>
	<div class="alert alert-warning" role="alert">
			<a href="/wp-login.php" title="Unit area" rel="home">Login</a>
	</div>
	<?php } ?>
	<div class="alert alert-primary" role="alert">
		<a href="https://endeavouresu.uk/glider-simulator-overview/" class="alert-heading">Simulator project</a><br />
		<a href="https://endeavouresu.uk/category/bsim/" class="alert-heading">Simulator blog</a><br />
		<a href="https://endeavouresu.uk/glider-simulator-videos/" class="alert-heading">Simulator videos</a>
	</div>
	<div class="alert alert-info" role="alert">
		<?php if ($logedin) { ?>
			<a href="https://endeavouresu.uk/forms/">Forms library</a><br />
			<a href="https://endeavouresu.uk/payments-and-banking-details/">Bank details</a><br />
			<a href="https://endeavouresu.uk/all-photos/">All photos</a><br />
		<?php } ?>
		<a href="https://endeavouresu.uk/all-videos/">All videos</a>
	</div>
 	<div class="alert alert-info m-0 p-0" role="alert">
	<div>
		<p class="font-weight-bolder m-0 p-0">Recent news:</p>
		<?php
		$r = new WP_Query(
			apply_filters(
				'widget_posts_args',
				array(
					'posts_per_page'      => 5,
					'no_found_rows'       => true,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
				)
			)
		);

		if ( $r->have_posts() ) { ?>
			<ul style="font-size:0.85em;">
				<?php foreach ( $r->posts as $recent_post ) : 
					$post_title   = get_the_title( $recent_post->ID );
					$title        = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)' );
					$aria_current = '';

					if ( get_queried_object_id() === $recent_post->ID ) {
						$aria_current = ' aria-current="page"';
					}
					?>
					<li>
						<a href="<?php the_permalink( $recent_post->ID ); ?>"<?php echo $aria_current; ?>><?php echo $title; ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php } ?>
		<p class="font-weight-bolder m-0 p-0">News feeds:</p>
		<?php 
			$homeurl = home_url();
			echo "<form action=\"" . esc_url($homeurl) . "\" method=\"get\">";
			$feeds_args = array(
				'orderby'      => 'name',
				'show_count'   => 1,
				'hierarchical' => 0,
				'echo' => 1,
				'name' => 'cat',
				'id' => 'cat',
				'show_option_none' => 'Select Category',
				'class' => '',
				);
			wp_dropdown_categories ( $feeds_args ); ?>
		</form>
		<script type='text/javascript'>
			(function() {
				var dropdown = document.getElementById( "cat" );
				function onCatChange() {
					if ( dropdown.options[ dropdown.selectedIndex ].value > 0 ) {
						dropdown.parentNode.submit();
					}
				}
				dropdown.onchange = onCatChange;
			})();
		</script>
		<br />
		<p class="font-weight-bolder m-0 p-0">Older news:</p>
		<select id="oldernews-dropdown" name="oldernews-dropdown">
			<option value="">Select month</option>
			<?php
			$archives_args = array(
				'type' => 'monthly',
				'show_post_count' => 1,
				'format' => 'option',
				'before' => '',
				'after' => '',
				'echo' => 1,
				'order' => 'DESC'
				);
			wp_get_archives( $archives_args ); ?>
		</select>
		<script type='text/javascript'>
			(function() {
				var dropdown = document.getElementById( "oldernews-dropdown" );
				function onNewsChange() {
					if ( dropdown.options[ dropdown.selectedIndex ].value !== '' ) {
						document.location.href = this.options[ this.selectedIndex ].value;
					}
				}
				dropdown.onchange = onNewsChange;
			})();
		</script>
	</div> <!-- /monthly -->
   </div>
   </div> <!-- /m2 p2 -->
  </div> <!-- /col /.blog-sidebar -->