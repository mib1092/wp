<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div class="wrap">
        <header class="header">
            <div class="navbar">
                <a href="<?php echo home_url(); ?>" class="logo">
                    <img src="<?php echo $logo; ?>" alt="<?php bloginfo('name'); ?>">
                </a>

                <?php wp_nav_menu(array(
                    'theme_location'  => 'main-menu', 
                    'menu'            => 'Main Menu',
                    'container'       => 'nav',
                    'container_class' => 'main-nav',
                    'container_id'    => false,
                    'items_wrap'      => '<ul>%3$s</ul>',
                    'depth'           => 1
                )); ?>
            </div>

            </section>
        </header>