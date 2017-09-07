<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Menu_handler {
//put your code here
    
    public function generate($menu_JSON) {
        $menus = json_decode($menu_JSON);
        $start = true;
        $open_child = FALSE;
        $open_parent = FALSE;
        $tag_html = "";
        $temp_level = 0;
        
        foreach ($menus as $menu) {
            if ($menu->SHOW_MENU == 0) continue;
            
            if ($menu->LEVEL_CHILD != $temp_level) {
                if ($open_child) {
                    $open_child = false;
                    $tag_html .= $this->ul_close();
                    $tag_html .= $this->li_close();
                } elseif (!$start) {
                    $open_child = true;
                    $tag_html .= $this->ul_open();
                }
            }
            
            if ($open_parent && $menu->LEVEL_CHILD == 1) {
                $open_parent = false;
                $tag_html .= $this->li_close();
            }
            if ($menu->LEVEL_CHILD == 1) {
                $open_parent = true;
                $tag_html .= $this->li_open();
            }
            
            if ($open_child) $tag_html .= $this->li_open();
            
            $tag_html .= $this->create_link($menu, $open_child);
            
            if ($open_child) $tag_html .= $this->li_close();
            
            $temp_level = $menu->LEVEL_CHILD;
            
            $start = false;
        }
        
        return $tag_html;
    }
    
    private function li_open() {
        return "<li>";
    }
    
    private function li_close() {
        return "</li>";
    }
    
    private function ul_open() {
        return '<ul class="nav nav-second-level">';
    }
    
    private function ul_close() {
        return '</ul>';
    }
    
    private function create_link($menu) {
        $label = $menu->NAME_MENU;
        
        if ($menu->FUNCTION_MENU == 'index') $url_link = base_url() . $menu->CONTROLLER_MENU;
        else $url_link = base_url() . $menu->CONTROLLER_MENU . '/' . $menu->FUNCTION_MENU;

        if ($menu->LEVEL_CHILD == 1) $label = '<span class="nav-label">' . $menu->NAME_MENU . '</span>';
        if ($menu->CONTROLLER_MENU === null) $url_link = "#";
        if ($menu->LEVEL_CHILD == 1 && $menu->HAVE_CHILD == 1) $label .= '<span class="fa arrow"></span>';

        return '<a href="' . $url_link . '"> ' . $label . ' </a>';
    }
}