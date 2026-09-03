<?php
/**
 * z_minimal Theme Functions & Definitions
 *
 * @package z_minimal
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * 1. 基础主题设置与特性支持
 */
function z_minimal_setup() {
    // 自动管理页面 <title>
    add_theme_support( 'title-tag' );

    // 支持特色图片
    add_theme_support( 'post-thumbnails' );

    // 启用 HTML5 语义化标记支持
    add_theme_support(
        'html5',
        array(
            'search-form',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        )
    );

    // 注册导航菜单位置
    register_nav_menus(
        array(
            'primary'      => __( '顶部主菜单 (Header Menu)', 'z_minimal' ),
            'footer_links' => __( '底部链接菜单 (Footer Links)', 'z_minimal' ),
        )
    );
}
add_action( 'after_setup_theme', 'z_minimal_setup' );

/**
 * 2. 引入主题样式表（纯本地零外部依赖）
 */
function z_minimal_scripts() {
    wp_enqueue_style(
        'z-minimal-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'z_minimal_scripts' );

/**
 * 3. 彻底禁用全站评论功能
 */
// 屏蔽前台文章与页面评论开放状态
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

// 移除文章和页面对评论的支持
function z_minimal_disable_comments_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
    remove_post_type_support( 'post', 'trackbacks' );
    remove_post_type_support( 'page', 'trackbacks' );
}
add_action( 'init', 'z_minimal_disable_comments_support' );

// 隐藏管理后台菜单中的评论项
function z_minimal_disable_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'z_minimal_disable_comments_admin_menu' );

// 从顶部管理工具条移除评论图标
function z_minimal_disable_comments_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'z_minimal_disable_comments_admin_bar', 999 );

/**
 * 4. 优化国内访问：禁用 WordPress 默认外部 Emoji (s.w.org) 资源
 */
function z_minimal_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    add_filter( 'tiny_mce_plugins', function( $plugins ) {
        return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
    } );

    add_filter( 'wp_resource_hints', function( $urls, $relation_type ) {
        if ( 'dns-prefetch' === $relation_type ) {
            $urls = array_filter( $urls, function( $url ) {
                return strpos( $url, 's.w.org' ) === false;
            } );
        }
        return $urls;
    }, 10, 2 );
}
add_action( 'init', 'z_minimal_disable_emojis' );

/**
 * 5. 注册 WordPress 原生外观自定义配置 (Customizer)
 */
function z_minimal_customize_register( $wp_customize ) {
    // 增加专属设置面板
    $wp_customize->add_section(
        'z_minimal_options_section',
        array(
            'title'       => __( '博客个性化设置', 'z_minimal' ),
            'priority'    => 35,
            'description' => __( '在此可配置首页简介文字、ICP 备案号、版权声明等信息。', 'z_minimal' ),
        )
    );

    // 首页简介文本 (支持多行换行)
    $wp_customize->add_setting(
        'z_minimal_intro_text',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        )
    );
    $wp_customize->add_control(
        'z_minimal_intro_text',
        array(
            'label'       => __( '首页顶部多行简介', 'z_minimal' ),
            'description' => __( '支持换行输入，将展示在首页主标题下方。留空则不展示。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'textarea',
        )
    );

    // ICP 备案号
    $wp_customize->add_setting(
        'z_minimal_icp_number',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'z_minimal_icp_number',
        array(
            'label'       => __( '工信部 ICP 备案号', 'z_minimal' ),
            'description' => __( '如：京ICP备12345678号，前台会自动链接至工信部官网。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'text',
        )
    );

    // 公网安备号 (可选)
    $wp_customize->add_setting(
        'z_minimal_police_beian',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'z_minimal_police_beian',
        array(
            'label'       => __( '公安联网备案号 (可选)', 'z_minimal' ),
            'description' => __( '如：京公网安备 11010102000001号。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'text',
        )
    );

    // 自定义版权文字
    $wp_customize->add_setting(
        'z_minimal_copyright_text',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'z_minimal_copyright_text',
        array(
            'label'       => __( '自定义版权声明', 'z_minimal' ),
            'description' => __( '留空则默认显示 "© 当前年份 站点名称"。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'text',
        )
    );

    // 备选底部自定义 HTML / 外链
    $wp_customize->add_setting(
        'z_minimal_footer_custom_html',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    $wp_customize->add_control(
        'z_minimal_footer_custom_html',
        array(
            'label'       => __( '底部备用自定义 HTML 内容', 'z_minimal' ),
            'description' => __( '若未在【外观】->【菜单】中绑定“底部链接菜单”，可在此处填写纯 HTML 链接。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'textarea',
        )
    );

    // 自定义页眉代码（统计代码、Meta验证等）
    $wp_customize->add_setting(
        'z_minimal_header_code',
        array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => function( $content ) {
                return current_user_can( 'unfiltered_html' ) ? $content : wp_kses_post( $content );
            },
        )
    );
    $wp_customize->add_control(
        'z_minimal_header_code',
        array(
            'label'       => __( '页眉自定义代码 (Header / 统计代码)', 'z_minimal' ),
            'description' => __( '输出在 </head> 之前。可放置百度统计、Google Analytics、站点所有权验证 Meta 标签等。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'textarea',
        )
    );

    // 自定义页脚代码（统计分析脚本等）
    $wp_customize->add_setting(
        'z_minimal_footer_code',
        array(
            'default'           => '',
            'transport'         => 'refresh',
            'sanitize_callback' => function( $content ) {
                return current_user_can( 'unfiltered_html' ) ? $content : wp_kses_post( $content );
            },
        )
    );
    $wp_customize->add_control(
        'z_minimal_footer_code',
        array(
            'label'       => __( '页脚自定义代码 (Footer / 统计分析JS)', 'z_minimal' ),
            'description' => __( '输出在 </body> 之前。常用于统计分析 JS 脚本等。', 'z_minimal' ),
            'section'     => 'z_minimal_options_section',
            'type'        => 'textarea',
        )
    );
}
add_action( 'customize_register', 'z_minimal_customize_register' );

/**
 * 6. 动态生成 SEO 标签 (Description / Canonical)
 */
function z_minimal_render_seo_meta() {
    $description = '';

    if ( is_single() || is_page() ) {
        $post_obj = get_queried_object();
        if ( $post_obj instanceof WP_Post ) {
            if ( ! empty( $post_obj->post_excerpt ) ) {
                $description = wp_strip_all_tags( $post_obj->post_excerpt );
            } else {
                $description = wp_trim_words( wp_strip_all_tags( $post_obj->post_content ), 40, '...' );
            }
        }
    } elseif ( is_home() || is_front_page() ) {
        $custom_intro = get_theme_mod( 'z_minimal_intro_text', '' );
        if ( ! empty( $custom_intro ) ) {
            $description = wp_strip_all_tags( $custom_intro );
        } else {
            $description = get_bloginfo( 'description' );
        }
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term && ! empty( $term->description ) ) {
            $description = wp_strip_all_tags( $term->description );
        } else {
            $description = sprintf( __( '%s 下的博客文章列表与按年归档。', 'z_minimal' ), single_term_title( '', false ) );
        }
    }

    if ( ! empty( $description ) ) {
        echo '<meta name="description" content="' . esc_attr( trim( preg_replace( '/\s+/', ' ', $description ) ) ) . '">' . "\n";
    }

    // 规范链接 (Canonical)
    if ( is_singular() ) {
        echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
    } elseif ( is_home() || is_front_page() ) {
        echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
    } elseif ( is_category() || is_tag() || is_tax() ) {
        $term = get_queried_object();
        if ( $term ) {
            $term_link = get_term_link( $term );
            if ( ! is_wp_error( $term_link ) ) {
                echo '<link rel="canonical" href="' . esc_url( $term_link ) . '">' . "\n";
            }
        }
    }
}
add_action( 'wp_head', 'z_minimal_render_seo_meta', 1 );

/**
 * 7. 输出页眉与页脚自定义代码（统计代码等）
 */
function z_minimal_output_header_code() {
    $header_code = get_theme_mod( 'z_minimal_header_code', '' );
    if ( ! empty( $header_code ) ) {
        echo "\n<!-- z_minimal Custom Header Code -->\n" . $header_code . "\n";
    }
}
add_action( 'wp_head', 'z_minimal_output_header_code', 99 );

function z_minimal_output_footer_code() {
    $footer_code = get_theme_mod( 'z_minimal_footer_code', '' );
    if ( ! empty( $footer_code ) ) {
        echo "\n<!-- z_minimal Custom Footer Code -->\n" . $footer_code . "\n";
    }
}
add_action( 'wp_footer', 'z_minimal_output_footer_code', 99 );

/**
 * 8. 菜单自动规范化与关于页面初始化
 * - 移除多余的“归档”菜单项（首页即归档）
 * - 确保“关于”页面存在并正确关联，避免死链
 */
function z_minimal_ensure_about_page_and_clean_menu() {
    // 确保系统内有已发布的“关于”页面
    $about_page = get_page_by_path( 'about' );
    if ( ! $about_page ) {
        $about_page = get_page_by_title( '关于' );
    }
    if ( ! $about_page ) {
        $about_id = wp_insert_post(
            array(
                'post_title'   => '关于',
                'post_name'    => 'about',
                'post_content' => "你好，欢迎来到我的关于页面！\n\n您可以在 WordPress 后台【页面】->【关于】中编辑修改此内容。",
                'post_status'  => 'publish',
                'post_type'    => 'page',
            )
        );
    } else {
        $about_id = $about_page->ID;
    }

    // 自动清理主菜单中的“归档”项，并将“关于”修正为关联页面实体
    $locations = get_nav_menu_locations();
    if ( ! empty( $locations['primary'] ) ) {
        $items = wp_get_nav_menu_items( $locations['primary'] );
        if ( is_array( $items ) ) {
            foreach ( $items as $item ) {
                if ( $item->title === '归档' ) {
                    wp_delete_post( $item->ID, true );
                } elseif ( $item->title === '关于' && ( $item->type === 'custom' || empty( $item->object_id ) ) && ! empty( $about_id ) ) {
                    update_post_meta( $item->ID, '_menu_item_type', 'post_type' );
                    update_post_meta( $item->ID, '_menu_item_object', 'page' );
                    update_post_meta( $item->ID, '_menu_item_object_id', $about_id );
                    update_post_meta( $item->ID, '_menu_item_url', '' );
                }
            }
        }
    }
}
add_action( 'init', 'z_minimal_ensure_about_page_and_clean_menu', 25 );

/**
 * 9. 智能解析 /about 等无伪静态环境下的页面访问
 * 当服务器未配置伪静态或者固定链接为朴素模式时，直接访问 /about 也能准确载入关于页面
 */
function z_minimal_smart_slug_fallback() {
    if ( is_front_page() || is_home() || is_404() ) {
        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
        $path = trim( parse_url( $request_uri, PHP_URL_PATH ), '/' );

        // 排除静态文件
        if ( ! empty( $path ) && ! preg_match( '/\.(jpg|jpeg|png|gif|css|js|ico|svg|txt|xml|woff|woff2)$/i', $path ) ) {
            // 优先检查是否为分类请求，如 /category/xxx
            if ( preg_match( '#^category/([^/]+)#i', $path, $cat_match ) ) {
                $cat_slug = urldecode( $cat_match[1] );
                $cat_obj = get_category_by_slug( $cat_slug );
                if ( ! $cat_obj ) {
                    $cat_obj = get_term_by( 'name', $cat_slug, 'category' );
                }
                if ( $cat_obj ) {
                    global $wp_query;
                    $wp_query->is_home           = false;
                    $wp_query->is_front_page     = false;
                    $wp_query->is_category       = true;
                    $wp_query->is_archive        = true;
                    $wp_query->is_404            = false;
                    $wp_query->queried_object    = $cat_obj;
                    $wp_query->queried_object_id = $cat_obj->term_id;
                    include get_template_directory() . '/index.php';
                    exit;
                }
            }

            $page = get_page_by_path( $path );
            if ( ! $page ) {
                $page = get_page_by_title( $path );
            }
            if ( $page && $page->post_status === 'publish' ) {
                global $wp_query, $post;
                $wp_query->is_home           = false;
                $wp_query->is_front_page     = false;
                $wp_query->is_page           = true;
                $wp_query->is_singular       = true;
                $wp_query->is_404            = false;
                $wp_query->queried_object    = $page;
                $wp_query->queried_object_id = $page->ID;
                $wp_query->posts             = array( $page );
                $wp_query->post_count        = 1;
                $post                        = $page;
                setup_postdata( $post );

                include get_template_directory() . '/page.php';
                exit;
            }
        }
    }
}
add_action( 'template_redirect', 'z_minimal_smart_slug_fallback', 1 );

/**
 * 10. 文章内容中的站外链接自动以新窗口打开，并补充 noopener noreferrer 安全属性
 */
function z_minimal_external_links_blank( $content ) {
    if ( empty( $content ) || ! is_singular() ) {
        return $content;
    }

    $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

    return preg_replace_callback(
        '/<a\s+([^>]*?)href=["\'](https?:\/\/[^"\']+)["\']([^>]*?)>/i',
        function( $matches ) use ( $site_host ) {
            $url  = $matches[2];
            $host = wp_parse_url( $url, PHP_URL_HOST );

            // 判断是否为非本站链接（外链）
            if ( ! empty( $host ) && strcasecmp( $host, $site_host ) !== 0 ) {
                $tag = $matches[0];

                // 自动附加新窗口打开属性
                if ( stripos( $tag, 'target=' ) === false ) {
                    $tag = str_replace( '<a ', '<a target="_blank" ', $tag );
                }

                // 自动附加安全防钓鱼与保护引用属性
                if ( stripos( $tag, 'rel=' ) === false ) {
                    $tag = str_replace( '<a ', '<a rel="noopener noreferrer" ', $tag );
                } elseif ( stripos( $tag, 'noopener' ) === false ) {
                    $tag = preg_replace( '/rel=["\'](.*?)["\']/i', 'rel="$1 noopener noreferrer"', $tag );
                }

                return $tag;
            }

            return $matches[0];
        },
        $content
    );
}
add_filter( 'the_content', 'z_minimal_external_links_blank', 20 );

