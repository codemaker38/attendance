<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class AddMenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin menu
        $menu = Menu::where('name', 'admin')->first();

        // Add a new menu item
        MenuItem::create([
            'menu_id'    => $menu->id,
            'title'      => 'Attendance',
            'url'        => '/attendances',
            'target'     => '_self',
            'icon_class' => 'voyager-calendar',
            'order'      => 99,
            'route'      => null,
            'parameters' => null,
            'parent_id'  => null,
        ]);
    }
}
