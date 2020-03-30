<?php

namespace modules\menu\admin;

use m\module;
use m\view;
use m\config;
use modules\pages\models\pages;
use modules\menu\models\menu;

class menu_by_type extends module {

    protected static $module = 'menu';

    public function _init()
    {
        config::set('per_page', 1000);

        $items = menu::call_static()->s([], [
            'site' => $this->site->id,
            'module' => static::$module,
        ], [1000])->all();

        $arr = [];

        if (!empty($items)) {
            foreach ($items as $item) {

                $page = empty($item['page']) ? '' : new pages($item['page']);

                $arr[] = $this->view->{'overview_menu_by_type_item'}->prepare([
                    'id' => $item['id'],
                    'name' => !empty($page->name) ? $page->name : $item['name'],
                    'path' => !empty($page->address) ? $page->get_path() : $item['path'],
                    'module' => static::$module,
                    'target' => $item['target'],
                ]);
            }
        }

        view::set('content', $this->view->overview_menu_by_type->prepare([
                'items' => implode("\n", $arr),
                'module_name' => static::$module,
            ]));
    }
}