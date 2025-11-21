<?php

namespace App\Http\Controllers;

abstract class Controller
{
    // You can add shared controller methods here if needed
    // For example, a method to set active menu
    protected function setActiveMenu($menu)
    {
        session(['active_menu' => $menu]);
    }
}
