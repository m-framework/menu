<?php

namespace modules\menu\client;

class footer_menu extends menu {

    protected $cache = false;

    public static $_name = '*Footer menu*';

    protected $css = [
        '/css/footer_menu.css'
    ];
}