<?php get_header(); ?>
<div class="row m-0 p-0">
	<div class="col-9 m-0 p-0 blog-main">
		<?php if ( have_posts() ) {
			// if ( bootstrapwp_checkfirstpost() != true ) { 
				next_posts_link('Earlier posts');
			// } ?>
			&nbsp;
			<?php
			// if ( bootstrapwp_checklastpost() != true ) { 
				previous_posts_link('More recent posts');
			// } 
			while ( have_posts() ) : the_post(); ?>
					<div class="blog-post">
						<h2 class="blog-post-title"><?php the_title(); ?></h2>
						<p class="blog-post-meta"><?php the_date(); ?> by <?php the_author(); ?></p>
						<?php the_content(); ?>
					</div><!-- /.blog-post -->
			<?php endwhile;

			// if ( bootstrapwp_checkfirstpost() != true ) { 
				next_posts_link('Earlier posts');
			// } ?>
			&nbsp;
			<?php
			// if ( bootstrapwp_checklastpost() != true ) { 
				previous_posts_link('More recent posts');
			// } 
		} ?>
	</div> <!-- /.blog-main -->
<?php get_sidebar(); ?>
</div> <!-- /row -->

<?php get_footer(); ?>