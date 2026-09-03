<?php
/**
 * The main template file for z_minimal
 *
 * Displays the homepage intro and year-grouped full archive list without pagination.
 *
 * @package z_minimal
 */

// 针对单页面或单篇文章的容错回退调度
if ( is_page() ) {
    include get_template_directory() . '/page.php';
    return;
}
if ( is_single() ) {
    include get_template_directory() . '/single.php';
    return;
}
if ( is_404() ) {
    include get_template_directory() . '/404.php';
    return;
}

get_header();
?>

<main id="main-content" class="site-main" role="main">
    <?php
    // 1. 如果是分类/标签/自定义分类页面，展示语义化归档头部
    if ( is_category() || is_tag() || is_tax() ) :
    ?>
        <header class="archive-header">
            <span class="archive-badge">
                <?php
                if ( is_category() ) {
                    esc_html_e( '分类归档', 'z_minimal' );
                } elseif ( is_tag() ) {
                    esc_html_e( '标签归档', 'z_minimal' );
                } else {
                    esc_html_e( '归档', 'z_minimal' );
                }
                ?>
            </span>
            <h1 class="archive-title"><?php single_term_title(); ?></h1>
            <?php if ( get_the_archive_description() ) : ?>
                <div class="archive-description"><?php the_archive_description(); ?></div>
            <?php endif; ?>
        </header>
    <?php
    // 2. 仅在真正的首页展示多行简介（避免污染分类归档页面）
    elseif ( is_home() || is_front_page() ) :
        $intro_text = get_theme_mod( 'z_minimal_intro_text', '' );
        if ( ! empty( $intro_text ) ) :
    ?>
        <section class="site-intro" aria-label="<?php esc_attr_e( '个人简介', 'z_minimal' ); ?>">
            <p><?php echo nl2br( esc_html( $intro_text ) ); ?></p>
        </section>
    <?php
        endif;
    endif;
    ?>

    <?php
    // 3. 构建文章查询参数（支持全量获取及分类/标签精准筛选）
    $query_args = array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => -1,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
    );

    // 如果处于分类归档，按对应分类 ID 精确筛选文章
    if ( is_category() ) {
        $query_args['cat'] = get_queried_object_id();
    } elseif ( is_tag() ) {
        $query_args['tag_id'] = get_queried_object_id();
    } elseif ( is_tax() ) {
        $term = get_queried_object();
        if ( $term ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ),
            );
        }
    }

    $archive_query = new WP_Query( $query_args );

    if ( $archive_query->have_posts() ) :
        $posts_by_year = array();

        while ( $archive_query->have_posts() ) :
            $archive_query->the_post();
            $year = get_the_date( 'Y' );
            $posts_by_year[ $year ][] = array(
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'date'      => get_the_date( 'Y/m/d' ),
                'c_date'    => get_the_date( 'c' ),
            );
        endwhile;
        wp_reset_postdata();

        // 4. 按年份聚合展示，严格符合 2026年 / 2026/08/23 标题 格式
        foreach ( $posts_by_year as $year => $year_posts ) :
        ?>
            <section class="year-archive" aria-labelledby="year-heading-<?php echo esc_attr( $year ); ?>">
                <h2 id="year-heading-<?php echo esc_attr( $year ); ?>" class="year-heading">
                    <?php echo esc_html( $year . '年' ); ?>
                </h2>
                <ul class="post-list" role="list">
                    <?php foreach ( $year_posts as $post_item ) : ?>
                        <li class="post-item">
                            <time class="post-date" datetime="<?php echo esc_attr( $post_item['c_date'] ); ?>">
                                <?php echo esc_html( $post_item['date'] ); ?>
                            </time>
                            <a class="post-title-link" href="<?php echo esc_url( $post_item['permalink'] ); ?>">
                                <?php echo esc_html( $post_item['title'] ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php
        endforeach;
    else :
    ?>
        <p class="empty-archive-msg">
            <?php
            if ( is_category() || is_tag() || is_tax() ) {
                esc_html_e( '该分类下暂无已发布的文章。', 'z_minimal' );
            } else {
                esc_html_e( '暂无已发布的博客文章。', 'z_minimal' );
            }
            ?>
        </p>
    <?php endif; ?>
</main>

<?php
get_footer();
