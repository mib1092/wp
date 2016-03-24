<?php
// loading styles and scripts
function load_style_script(){
    // disable the connection native Wordpress jquery (except admin panel)
    if ( !is_admin() ) wp_deregister_script('jquery');

    wp_enqueue_style('fonts', '//fonts.googleapis.com/css?family=Roboto:400,100italic,300,100,300italic,400italic,500,500italic,700,700italic,900,900italic|Roboto+Condensed:400,300,300italic,400italic,700,700italic|Raleway:400,600,800', array(), null);
    wp_enqueue_style('font-awesome.min', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), null);
    wp_enqueue_style('owl.carousel.min', get_template_directory_uri() . '/css/owl.carousel.min.css', array(), null );
    wp_enqueue_style('owl.transitions', get_template_directory_uri() . '/css/owl.transitions.css', array(), null );
    wp_enqueue_style('sweetalert', get_template_directory_uri() . '/css/sweetalert.css', array(), null );
    wp_enqueue_style('styles', get_template_directory_uri() . '/css/styles.css', array(), null );
    wp_enqueue_style('media', get_template_directory_uri() . '/css/media.css', array(), null );
    wp_enqueue_style('style', get_stylesheet_uri(), array(), null );

    wp_enqueue_script('modernizr.min', '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js', array(), '2.8.3', false );
    wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', array(), '1.11.3', false );
    wp_enqueue_script('scroll', get_template_directory_uri() . '/js/scroll.js', array('jquery'), null, true );
    wp_enqueue_script('owl.carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'), null, true );
    wp_enqueue_script('sweetalert.min', get_template_directory_uri() . '/js/sweetalert.min.js', array('jquery'), null, true );
    wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js', array('jquery'), null, true );
}
add_action('wp_enqueue_scripts', 'load_style_script');


// add ie conditional html5 shiv to header
function add_ie_html5_shiv () {
    global $is_IE;
    if ($is_IE) {
        echo '<!--[if lt IE 9]>';
        echo '<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>';
        echo '<![endif]-->';
    }
}
add_action('wp_head', 'add_ie_html5_shiv');


// logo at the entrance to the admin panel
function my_custom_login_logo(){
    echo '<style type="text/css">
    h1 a {height:142px !important; width:190px !important; background-size:contain !important; background-image:url('.get_bloginfo("template_directory").'/img/main_logo.png) !important;}
    </style>';
}
add_action('login_head', 'my_custom_login_logo');

add_filter( 'login_headerurl', create_function('', 'return get_home_url();') );
add_filter( 'login_headertitle', create_function('', 'return false;') );


// когда введены неправильный логин или пароль, никакой информации, объясняющей ситуацию, не появится!
add_filter('login_errors',create_function('$a', "return null;"));


// delete unnecessary items in wp_head
remove_action( 'wp_head', 'feed_links_extra', 3 ); 
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); 
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'wp_generator' );


// удаление оборачиваемого тега <p> из картинок в контенте
function filter_ptags_on_images($content){
   return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}
add_filter('the_content', 'filter_ptags_on_images');


// automatic generation of the tag <title>
add_theme_support( 'title-tag' );


// support thumbnails
if ( function_exists( 'add_theme_support' ) ) {
    add_theme_support( 'post-thumbnails' );
}


// support menus
if ( function_exists( 'register_nav_menus' ) ) {
    register_nav_menus(array(
        'main-menu'     => 'Main Menu',
        'footer-menu'   => 'Footer Menu'
    ));
}
function main_menu(){
    wp_list_pages('title_li=&');
}


// for excerpts
function new_excerpt_more( $more ) {
    return '<a class="three-points" href=' . get_permalink() . '>' . '...</a>';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );


function new_excerpt_length($length) {
  return 30;
}
add_filter('excerpt_length', 'new_excerpt_length');


// register widget panels
function register_my_widgets(){
    register_sidebar( array(
        'name'          => 'Sidebar',
        'id'            => 'sidebar',
        'class'         => 'sidebar',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => "</section>\n",
        'before_title'  => '<h4>',
        'after_title'   => "</h4>\n",
    ) );
}
add_action( 'widgets_init', 'register_my_widgets' );


// pagination function
function my_pagenavi() {
    global $wp_query;

    $big = 999999999;

    $args = array(
        'base'          => str_replace( $big, '%#%', get_pagenum_link( $big ) )
        ,'format'       => '?paged=%#%'
        ,'current'      => max( 1, get_query_var('paged') )
        ,'total'        => $wp_query->max_num_pages
        ,'end_size'     => 0
        ,'mid_size'     => 2
        ,'type'         => 'list'
        ,'prev_text'    => '<'
        ,'next_text'    => '>'
    );

    $result = paginate_links( $args );
    $result = str_replace( '/page/1/', '', $result );

    echo $result;
}


/* Хак на перезапись параметра guid при публикации или обновлении поста в админке (записывается пермалинк в текущей структуре)
--------------------------------------------------------------------------------------------------------------------------------- */
function guid_write( $id ){
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // если это автосохранение
    
    global $wpdb;
    
    if( $id = (int) $id )
        $wpdb->query("UPDATE $wpdb->posts SET guid='". get_permalink($id) ."' WHERE ID=$id LIMIT 1");
}
add_action ('save_post', 'guid_write', 100);



// neighbor posts
function neighbor_posts($post_id) {
    global $wpdb;
 
    // Нам нужна категория поста. Если у поста несколько категорий, будет взята первая
    $category = get_the_category($post_id);
    $cat_id = $category[0]->cat_ID;
 
    // Узнаём сколько записей добавлено до
    $all_left_num = $wpdb->get_var("select count($wpdb->posts.ID) from $wpdb->posts join $wpdb->term_relationships on  " .
                           "($wpdb->term_relationships.object_id = $wpdb->posts.ID) where ($wpdb->posts.ID < $post_id) " .
                           "and ($wpdb->term_relationships.term_taxonomy_id = $cat_id) and ($wpdb->posts.post_status='publish')");
    // А сколько после
    $all_right_num = $wpdb->get_var("select count($wpdb->posts.ID) from $wpdb->posts join $wpdb->term_relationships on  " .
                           "($wpdb->term_relationships.object_id = $wpdb->posts.ID) where ($wpdb->posts.ID > $post_id) " .
                           "and ($wpdb->term_relationships.term_taxonomy_id = $cat_id) and($wpdb->posts.post_status='publish')");
 
    $num_left = 1; 
    $num_right = 2; // Нам нужно по две записи с каждой стороны от нашей
 
    if ($all_left_num < 1) // Если слева нет 1 записи, компенсируем правыми
        $num_right += (1 - $all_left_num);
    if ($all_right_num < 2) // Если справа нет 2 записей, компенсируем левыми
        $num_left += (2 - $all_right_num);
 
    // Теперь можно запросить сами записи. Для левых (предыдущих) задаём сортировку по ID по убыванию
    // Таким образом гарантируем, что это будут именно ближайшие записи
    $left = $wpdb->get_results("select $wpdb->posts.* from $wpdb->posts join $wpdb->term_relationships on  " .
                           "($wpdb->term_relationships.object_id = $wpdb->posts.ID) where ($wpdb->posts.ID < $post_id) " .
                           "and ($wpdb->term_relationships.term_taxonomy_id = $cat_id) and ($wpdb->posts.post_status='publish') " .
                           "order by $wpdb->posts.ID desc limit $num_left");
 
    // Для правых сортировку можно было не задавать, но так, по образу и подобию 
    $right = $wpdb->get_results("select $wpdb->posts.* from $wpdb->posts join $wpdb->term_relationships on  " .
                           "($wpdb->term_relationships.object_id = $wpdb->posts.ID) where ($wpdb->posts.ID > $post_id) " .
                           "and ($wpdb->term_relationships.term_taxonomy_id = $cat_id) and($wpdb->posts.post_status='publish') " .
                           "order by $wpdb->posts.ID asc limit $num_right");
    // здесь будут все посты, заголовки которых нам надо отобразить
    $posts = array ();
 
    // Левые отсортированы по убыванию, поэтому добавляем каждый следующий в начало $posts
    // Таким образом в $posts они будут отсортированы по возрастанию
    foreach ($right as $post)
        array_unshift($posts, $post);
 
    $posts[] = get_post($post_id);
 
    foreach ($left as $post)
        $posts[] = $post;
 
    // Выводим ссылки на всю эту фигню
    echo "<ul class='portfolio-list'>";
    foreach ($posts as $post) {
        if ($post->ID != $post_id) {
            $p_thumb = get_post_meta($post->ID, 'wpcf-proj-thumb', true);
            $p_title = get_post_meta($post->ID, 'wpcf-proj-title', true);
            $title = $post->post_title;
            echo "<li>";
            echo "<a href='". get_permalink($post->ID) ."'>";
            if ($p_thumb) { echo "<img src='$p_thumb' width='194' height='152' alt='$p_title' title='$title' >"; };
            echo "<div class='mask'><h4>$p_title</h4></div>";
            echo "</a>";
            echo "</li>";
        } else {
            // echo $post->post_title;
        };
    }
    echo "</ul>";
}