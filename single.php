<?php
/**
 * The template for displaying all single posts
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
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'article-entry' ); ?> itemscope itemtype="https://schema.org/BlogPosting">
            <header class="article-header">
                <time class="article-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePublished">
                    <?php echo esc_html( get_the_date( 'Y/m/d' ) ); ?>
                </time>
                <meta itemprop="dateModified" content="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>">
                <span itemprop="author" itemscope itemtype="https://schema.org/Person" style="display:none;">
                    <meta itemprop="name" content="<?php echo esc_attr( get_the_author() ); ?>">
                </span>
                <h1 class="article-title" itemprop="headline"><?php the_title(); ?></h1>
            </header>

            <div class="article-content" itemprop="articleBody">
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

            <nav class="article-navigation" role="navigation" aria-label="<?php esc_attr_e( '文章跳转导航', 'z_minimal' ); ?>">
                <div class="nav-previous">
                    <?php
                    $prev_post = get_previous_post();
                    if ( ! empty( $prev_post ) ) :
                    ?>
                        <span class="nav-meta-label">← <?php esc_html_e( '上一篇', 'z_minimal' ); ?></span>
                        <a class="nav-link-title" href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>">
                            <?php echo esc_html( get_the_title( $prev_post->ID ) ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="nav-next">
                    <?php
                    $next_post = get_next_post();
                    if ( ! empty( $next_post ) ) :
                    ?>
                        <span class="nav-meta-label"><?php esc_html_e( '下一篇', 'z_minimal' ); ?> →</span>
                        <a class="nav-link-title" href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>">
                            <?php echo esc_html( get_the_title( $next_post->ID ) ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </article>
    <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
