<?php

namespace modules\menu\models;

use m\model;
use m\cache;
use m\registry;
use modules\pages\models\pages;

class menu extends model
{
    public $_table = 'menu';
    protected $_sort = ['sequence' => 'ASC'];

    protected $fields = [
        'id' => 'int',
        'site' => 'int',
        'module' => 'varchar',
        'page' => 'int',
        'path' => 'varchar',
        'name' => 'varchar',
        'target' => 'varchar',
        'sequence' => 'int',
    ];

    static public function get($type = null)
    {
        $_menu = menu::call_static()
            ->s(
                [],
                ['site=' . registry::get('site')->id, 'module' => $type],
                [1000],
                ['sequence' => 'ASC']
            )
            ->all();

        if (!empty($_menu)) {

            $pages = pages::get_pages();

            foreach ($_menu as $k => $_menu_item) {

                if (empty($_menu_item['page']) && empty($_menu_item['path'])) {
                    continue;
                }

                $address = '';
                $name = '';

                if (!empty($_menu_item['page'])) {

                    if (empty($pages[$_menu_item['page']]) || empty($pages[$_menu_item['page']]['name'])
                        || empty($pages[$_menu_item['page']]['address'])) {
                        unset($_menu[$k]);
                        continue;
                    }

                    $address = $pages[$_menu_item['page']]['address'];
                    $name = $pages[$_menu_item['page']]['name'];
                }
                
                if (!empty($_menu_item['path'])) {
                    $address = $_menu_item['path'];
                }
                
                if (!empty($_menu_item['name'])) {
                    $name = $_menu_item['name'];
                }

                if (empty($address) && empty($name)) {
                    unset($_menu[$k]);
                    continue;
                }

                $_menu[$k]['address'] = $address;
                $_menu[$k]['name'] = $name;
            }
        }

        return $_menu;
    }

    public function footer_links()
    {
        $links = $this->get('footer_menu');
        $arr = [];
        $language = registry::get('language');

        if (!empty($links))
            foreach ($links as $link) {
                $arr[] = '<a href="//' . $_SERVER['HTTP_HOST'] . '/' . $language . $link->address . '">' .
                    $link->name . '</a>';
            }

        return $arr;
    }

    public function get_type_name()
    {
        $_n = 'modules\\menu\\client\\' . $this->module;

        if (class_exists($_n)) {
            $vars = get_class_vars($_n);
            return empty($vars['_name']) ? $this->module : $vars['_name'];
        }

        return '*' . $this->module . '*';
    }
}