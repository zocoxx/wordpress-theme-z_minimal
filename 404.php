<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package z_minimal
 */

get_header();
?>

<main id="main-content" class="site-main" role="main">
    <div class="error-404-container">
        <div class="error-404-title">404</div>
        <p class="error-404-msg"><?php esc_html_e( '抱歉，您访问的页面不存在或已被移除。', 'z_minimal' ); ?></p>
        <a class="back-home-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php esc_html_e( '返回首页', 'z_minimal' ); ?>
        </a>
    </div>
</main>

<?php
get_footer();
