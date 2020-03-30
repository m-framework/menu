<?php

namespace modules\menu\client;

use libraries\helper\url;
use m\config;
use
    m\module,
    m\cache,
    m\registry,
    modules\pages\models\pages,
    modules\menu\models,
    m\view;

class menu extends module {

    public function _init()
    {
        $menu_items = models\menu::get($this->module_name);

        $pages_tree = $this->page->get_pages_tree();

        if (empty($pages_tree) || empty($menu_items) || !isset($this->view->{$this->module_name})
            || !isset($this->view->{$this->module_name . '_item'}))
            return false;

        $menu_arr = [];

        $item_view = $this->view->{$this->module_name . '_item'};

        foreach ($menu_items as $menu_item) {

            if (!empty($menu_item['path']) && !empty($menu_item['name'])) {

                $menu_arr[] = $item_view->prepare([
                    'address' => url::to($menu_item['path']),
                    'link' => url::to($menu_item['path']),
                    'target' => $menu_item['target'],
                    'name' =>$menu_item['name'],
                    'text' =>$menu_item['name'],
                    'additional_class' => '',
                    'sub_menu' => '',
                ]);

                continue;
            }

            if (!empty($menu_item['page']) && (empty($pages_tree) || empty($pages_tree[$menu_item['page']])))
                continue;

            $pages_tree[$menu_item['page']]['target'] = $menu_item['target'];
            
            if (!empty($menu_item['name'])) {
                $pages_tree[$menu_item['page']]['name'] = $menu_item['name'];
            }

            $page_menu = $this->wrap_recursively($item_view, $pages_tree[$menu_item['page']]);

            $menu_arr[] = $page_menu;
        }

        if (empty($menu_arr))
            return false;

        view::set($this->module_name, $this->view->{$this->module_name}->prepare([
            'links' => implode('', $menu_arr),
        ]));

        unset($pages);
        unset($pages_tree);

        return true;
    }

    private function wrap_recursively($view_item, $pages_tree, $start_path = null)
    {
        if (!is_array($pages_tree))
            return '';

        $tmp = [];

        if (empty($start_path))
            $start_path = '';

        $start_path .= $pages_tree['address'];

        if (!empty($pages_tree['sub_pages']))
        foreach ($pages_tree['sub_pages'] as $page_item) {
            $tmp_path = empty($page_item['address']) && $page_item['address'] !== '/' ? '' :
                $start_path . $page_item['address'] ;

            $sub_pages = empty($page_item['sub_pages']) ? [] : $page_item['sub_pages'];

            $sub_menu = $this->wrap_recursively($sub_pages, $tmp_path);

            $n = empty($page_item['sequence']) ? count($tmp) - 1 : $page_item['sequence'];

            $tmp[$n] = $view_item->prepare([
                'address' => url::to($tmp_path),
                'link' => url::to($tmp_path),
                'target' => $pages_tree['target'],
                'name' => $page_item['name'],
                'text' => $page_item['name'],
                'additional_class' => empty($sub_menu) || strlen(trim($sub_menu)) == 0 ? '' : '',
                'sub_menu' => $sub_menu,
            ]);
        }

        ksort($tmp);

        return $view_item->prepare([
            'address' => url::to($start_path),
            'link' => url::to($start_path),
            'target' => $pages_tree['target'],
            'name' => $pages_tree['name'],
            'text' => $pages_tree['name'],
            'additional_class' => empty($tmp) ? '' : 'drop-down',
            'sub_menu' => implode("\n", $tmp),
        ]);
    }
}