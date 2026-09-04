<?php
/**
 * The header for z_minimal theme
 *
 * @package z_minimal
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script>
    /* 毫秒级主题模式初始化，杜绝闪烁 (FOUC) */
    (function() {
        var mode = localStorage.getItem('z_minimal_theme_mode') || 'system';
        var isDark = mode === 'dark' || (mode === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme-mode', mode);
    })();
    </script>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-wrapper">
    <header class="site-header" role="banner">
        <div class="site-branding">
            <?php if ( is_home() || is_front_page() ) : ?>
                <h1 class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                </h1>
            <?php else : ?>
                <span class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                </span>
            <?php endif; ?>
        </div>

        <nav class="site-navigation" role="navigation" aria-label="<?php esc_attr_e( '主菜单', 'z_minimal' ); ?>">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'depth'          => 1,
                    )
                );
            } else {
                // 未设置菜单时的友好默认回退
                echo '<ul class="nav-menu"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'z_minimal' ) . '</a></li></ul>';
            }
            ?>

            <div class="nav-actions">
                <!-- RSS 订阅图标按钮 (通过后台【外观】->【自定义】开关控制显隐) -->
                <?php if ( get_theme_mod( 'z_minimal_show_rss', true ) ) : ?>
                    <a href="<?php echo esc_url( get_feed_link() ); ?>" class="rss-link-btn" target="_blank" rel="noopener noreferrer" title="<?php esc_attr_e( 'RSS 订阅', 'z_minimal' ); ?>" aria-label="<?php esc_attr_e( 'RSS 订阅', 'z_minimal' ); ?>">
                        <svg class="rss-icon" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 11a9 9 0 0 1 9 9"></path>
                            <path d="M4 4a16 16 0 0 1 16 16"></path>
                            <circle cx="5" cy="19" r="1"></circle>
                        </svg>
                    </a>
                <?php endif; ?>

                <!-- 单一图标三态外观切换按钮 (点击循环切换：跟随系统 -> 浅色模式 -> 深色模式) -->
                <button type="button" class="theme-toggle-single-btn" id="theme-toggle-single-btn" aria-label="<?php esc_attr_e( '切换外观主题', 'z_minimal' ); ?>" title="<?php esc_attr_e( '当前外观：跟随系统（点击切换）', 'z_minimal' ); ?>">
                    <!-- 跟随系统图标 -->
                    <svg class="theme-icon icon-system" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <!-- 浅色模式图标 -->
                    <svg class="theme-icon icon-light" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    <!-- 深色模式图标 -->
                    <svg class="theme-icon icon-dark" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </button>
            </div>
        </nav>
    </header>

    <script>
    /* 单一图标三态外观模式切换逻辑 */
    (function() {
        var titles = {
            'system': '当前外观：跟随系统（点击切换为浅色模式）',
            'light':  '当前外观：浅色模式（点击切换为深色模式）',
            'dark':   '当前外观：深色模式（点击切换为跟随系统）'
        };

        function applyThemeMode(mode) {
            var isDark = mode === 'dark' || (mode === 'system' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.setAttribute('data-theme', isDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme-mode', mode);

            var toggleBtn = document.getElementById('theme-toggle-single-btn');
            if (toggleBtn) {
                var titleText = titles[mode] || titles['system'];
                toggleBtn.setAttribute('title', titleText);
                toggleBtn.setAttribute('aria-label', titleText);
            }
        }

        var currentMode = localStorage.getItem('z_minimal_theme_mode') || 'system';
        applyThemeMode(currentMode);

        document.addEventListener('DOMContentLoaded', function() {
            var currentMode = localStorage.getItem('z_minimal_theme_mode') || 'system';
            applyThemeMode(currentMode);

            var toggleBtn = document.getElementById('theme-toggle-single-btn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    var mode = localStorage.getItem('z_minimal_theme_mode') || 'system';
                    var nextMode = 'system';
                    if (mode === 'system') {
                        nextMode = 'light';
                    } else if (mode === 'light') {
                        nextMode = 'dark';
                    } else {
                        nextMode = 'system';
                    }
                    localStorage.setItem('z_minimal_theme_mode', nextMode);
                    applyThemeMode(nextMode);
                });
            }

            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                    var mode = localStorage.getItem('z_minimal_theme_mode') || 'system';
                    if (mode === 'system') {
                        applyThemeMode('system');
                    }
                });
            }

            // 自动为正文中的站外链接添加 target="_blank" 及安全属性
            var contentLinks = document.querySelectorAll('.article-content a');
            var localHost = window.location.hostname;
            for (var k = 0; k < contentLinks.length; k++) {
                var linkEl = contentLinks[k];
                if (linkEl.hostname && linkEl.hostname !== localHost) {
                    linkEl.setAttribute('target', '_blank');
                    var relVal = linkEl.getAttribute('rel') || '';
                    if (relVal.indexOf('noopener') === -1) {
                        linkEl.setAttribute('rel', (relVal + ' noopener noreferrer').trim());
                    }
                }
            }

            // 原生图片懒加载平滑淡入监听（渐进增强）
            var articleImgs = document.querySelectorAll('.article-content img');
            for (var m = 0; m < articleImgs.length; m++) {
                var imgEl = articleImgs[m];
                if (imgEl.complete) {
                    imgEl.classList.add('is-loaded');
                } else {
                    imgEl.addEventListener('load', function() {
                        this.classList.add('is-loaded');
                    });
                }
            }
        });
    })();
    </script>
