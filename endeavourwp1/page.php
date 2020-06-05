<?php get_header(); ?>
<div class="row m-0 p-0">
  <div class="col-10 m-0 p-0">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	  	<?php the_content(); ?>
	<?php endwhile; else: ?>
		<p><?php _e('Sorry, this page does not exist or there is no content for it, yet.'); ?></p>
	<?php endif; ?>
  </div> <!-- /col -->
  <?php get_sidebar(); ?>
</div> <!-- /row -->
<?php get_footer(); ?>