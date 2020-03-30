<?php

namespace modules\menu\client;

class horizontal_menu extends menu {

    protected $cache = true;

    public static $_name = '*Horizontal menu*';

    protected $css = [
        '/css/horizontal_menu.css'
    ];
}