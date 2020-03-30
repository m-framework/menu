<?php

namespace modules\menu\admin;

use m\config;
use m\module;
use m\i18n;
use m\registry;
use m\view;
use m\form;
use modules\menu\models\menu;
use modules\pages\models\pages;
use modules\pages\models\pages_types;
use modules\pages\models\pages_types_modules;
use modules\sites\models\sites;

class edit extends module {

    public function _init()
    {
        if (!isset($this->view->{'menu_' . $this->name . '_form'})) {
            return false;
        }

        $item = new menu(!empty($this->get->edit) ? $this->get->edit : null);

        if (!empty($item->id)) {
            view::set('page_title', '<h1><i class="fa fa-chain"></i> *Edit a menu link* ' . (empty($item->name) ? '' : '`' . $item->name . '`') . '</h1>');
            registry::set('title', i18n::get('Edit a menu link'));

            registry::set('breadcrumbs', [
                '/' . config::get('admin_panel_alias') . '/menu' => '*Navigation menus*',
                '/' . config::get('admin_panel_alias') . '/menu/' . str_replace('_menu', '', $item->module) => $item->get_type_name(),
                '/' . config::get('admin_panel_alias') . '/menu/edit/' . $item->id => '*Edit a menu link*',
            ]);
        }
        else {
            view::set('page_title', '<h1><i class="fa fa-chain"></i> *Add new menu link*</h1>');
            registry::set('title', i18n::get('Add new menu link'));
        }

        if (empty($item->site)) {
            $item->site = $this->site->id;
        }
        if (empty($item->module) && !empty($this->get->add)) {
            $item->module = $this->get->add;
        }
        if (empty($item->target)) {
            $item->target = '_self';
        }

        $pages_tree = $this->page->get_pages_tree();

        if (empty($pages_tree)) {
            $this->page->prepare_page([]);
            $pages_tree = $this->page->get_pages_tree();
        }

        $pages_arr = empty($pages_tree) ? [] : pages::options_arr_recursively($pages_tree, '');


        new form(
            $item,
            [
                'module' => [
                    'field_name' => i18n::get('Menu type'),
                    'related' => [
                        ['value' => 'horizontal_menu', 'name' => '*Horizontal menu*'],
                        ['value' => 'vertical_menu', 'name' => '*Vertical menu*'],
                        ['value' => 'footer_menu', 'name' => '*Footer menu*'],
                    ],
                    'required' => true,
                ],
                'page' => [
                    'field_name' => i18n::get('Page'),
                    'related' => $pages_arr,
                ],
                'path' => [
                    'type' => 'varchar',
                    'field_name' => i18n::get('Path'),
                ],
                'name' => [
                    'type' => 'varchar',
                    'field_name' => i18n::get('Name in menu'),
                ],
                'target' => [
                    'field_name' => i18n::get('Target'),
                    'related' => [
                        ['value' => '_blank', 'name' => '*In new tab*'],
                        ['value' => '_self', 'name' => '*In same tab*'],
                    ],
                    'required' => true,
                ],
            ],
            [
                'form' => $this->view->{'menu_' . $this->name . '_form'},
                'varchar' => $this->view->edit_row_varchar,
                'related' => $this->view->edit_row_related,
                'saved' => $this->view->edit_row_saved,
                'error' => $this->view->edit_row_error,
            ]
        );


    }
}