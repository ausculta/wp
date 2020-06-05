<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
 
get_header(); ?>

<div class="row m-0 p-0">
  <div class='col-10 m-0 p-0'>
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">
        <?php
        // Start the loop.
        while ( have_posts() ) : the_post();
            get_template_part( 'content', get_post_format() ); ?>
	
			<div class="d-flex justify-content-between bd-highlight mb-3">
				<div class="p-2 bd-highlight"><?php previous_post_link('&laquo; %link', '%title'); ?></div>
				<div class="p-2 bd-highlight"><?php the_title('<h3 class="text-center font-weight-bolder">', '</h3>', true); ?></div>
				<div class="p-2 bd-highlight"><?php next_post_link('%link &raquo;', '%title'); ?></div>
			</div>
	
			<?php the_content();
 
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
 
            // Previous/next post navigation.
            the_post_navigation( array(
                'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'twentyfifteen' ) . '</span> ' .
                    '<span class="screen-reader-text">' . __( 'Next post:', 'twentyfifteen' ) . '</span> ' .
                    '<span class="post-title">%title</span>',
                'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'twentyfifteen' ) . '</span> ' .
                    '<span class="screen-reader-text">' . __( 'Previous post:', 'twentyfifteen' ) . '</span> ' .
                    '<span class="post-title">%title</span>',
            ) );
 
        // End the loop.
        endwhile;
        ?>
 
        </main><!-- .site-main -->
    </div><!-- .content-area -->
   </div> <!-- /col -->
  <?php get_sidebar(); ?>
</div> <!-- /row -->

<?php get_footer(); ?>