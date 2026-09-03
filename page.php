<?php
/**
 * The template for displaying all pages
 *
 * @package z_minimal
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">
    <?php
    while ( have_posts() ) :
        the_post();
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'article-entry' ); ?>>
            <header class="article-header">
                <h1 class="article-title"><?php the_title(); ?></h1>
            </header>

            <div class="article-content">
                <?php
                the_content();

                wp_link_pages(
                    array(
                        'before' => '<div class="page-links">' . esc_html__( '分页:', 'z_minimal' ),
                        'after'  => '</div>',
                    )
                );
                ?>
            </div>
        </article>
    <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
