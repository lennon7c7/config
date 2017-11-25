<?php
/**
 * Displays a navigation menu
 * @param string $menu
 * @param string $container
 * @param string $top
 * @param string $items_wrap
 * @param string $sub
 */
function ats_nav_menu(
    $menu = 'header',
    $container = '<div class="menu-header-container">%1$s</div>',
    $top = '<a href="%1$s" class="top-menu"><h2>%2$s</h2></a>',
    $items_wrap = '<ul class="sub-menu">%1$s</ul>',
    $sub = '<li><a href="%1$s">%2$s</a></li>'
)
{
    $html = '';
    if (!$menu) {
        echo $html;
    }

    if (!$top) {
        $top = '<a href="%1$s">%2$s</a>';
    }

    if (!$sub) {
        $sub = '<a href="%1$s">%2$s</a>';
    }

    $menu_items = wp_get_nav_menu_items(wp_get_nav_menu_object($menu)->term_id);
    if (!$menu_items) {
        echo $html;
    }

    $menu_item_parent = array_shift($menu_items);
    foreach ($menu_items as $key => $value) {
        if ($menu_item_parent->ID != $value->menu_item_parent) continue;

        $html .= sprintf($sub, $value->url, $value->title);
    }

    if ($items_wrap) {
        $html = sprintf($items_wrap, $html);
    }

    $top = sprintf($top, $menu_item_parent->url, $menu_item_parent->title);
    $html = $top . $html;

    if ($container) {
        $html = sprintf($container, $html);
    }

    echo $html;
}
