<?php
/**
 * The template for displaying the footer
 *
 * @package z_minimal
 */
?>
    <footer class="site-footer" role="contentinfo">
        <?php
        // 1. 备案号上方一行自定义链接（优先使用【外观】->【菜单】中配置的“底部链接菜单”，支持后台可视化编辑）
        $has_footer_menu = has_nav_menu( 'footer_links' );
        $custom_footer_html = get_theme_mod( 'z_minimal_footer_custom_html', '' );

        if ( $has_footer_menu ) :
        ?>
            <nav class="footer-links-row" aria-label="<?php esc_attr_e( '底部链接', 'z_minimal' ); ?>">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'footer_links',
                        'menu_class'     => 'footer-links-menu',
                        'container'      => false,
                        'depth'          => 1,
                    )
                );
                ?>
            </nav>
        <?php elseif ( ! empty( $custom_footer_html ) ) : ?>
            <div class="footer-links-row footer-custom-html">
                <?php echo wp_kses_post( $custom_footer_html ); ?>
            </div>
        <?php endif; ?>

        <?php
        // 2. 工信部备案号与公安备案号
        $icp_number = get_theme_mod( 'z_minimal_icp_number', '' );
        $police_beian = get_theme_mod( 'z_minimal_police_beian', '' );

        if ( ! empty( $icp_number ) || ! empty( $police_beian ) ) :
        ?>
            <div class="footer-beian-row">
                <?php if ( ! empty( $icp_number ) ) : ?>
                    <a href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer nofollow">
                        <?php echo esc_html( $icp_number ); ?>
                    </a>
                <?php endif; ?>
                <?php if ( ! empty( $police_beian ) ) : ?>
                    <span class="police-beian"><?php echo esc_html( $police_beian ); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php
        // 3. 版权信息
        $copyright = get_theme_mod( 'z_minimal_copyright_text', '' );
        if ( empty( $copyright ) ) {
            $copyright = '© ' . date_i18n( 'Y' ) . ' ' . get_bloginfo( 'name' );
        }
        ?>
        <div class="footer-copyright">
            <span><?php echo esc_html( $copyright ); ?></span>
        </div>
    </footer>
</div><!-- .site-wrapper -->

<?php wp_footer(); ?>
</body>
</html>
