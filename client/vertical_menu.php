<?php

namespace modules\menu\client;

class vertical_menu extends menu {

    protected $cache = false;

    public static $_name = '*Vertical menu*';

    protected $css = [
        '/css/vertical_menu.css'
    ];
}