<?php get_header(); ?>

    <div class="content">
        
        <div class="content">
            
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                <?php the_content(); ?>

            <?php endwhile; else: endif; ?>

        </div><!--/content-->   

        <?php get_sidebar(); ?>
        
    </div><!--/Content-->

<?php get_footer(); ?>