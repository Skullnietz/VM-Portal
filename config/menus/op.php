<?php
return[
    'menu' => [
        // Navbar items:
        
        [
            'text' => 'Vendings',
            'url' => 'admin/settings',
            'icon_color' => 'green',
            'icon' => 'fas fa-fw fa-building',
            'submenu' => [
                [
                    'text' => 'Inventario Vendings',
                    'icon_color' => 'green',
                    'url' => 'op/op-vendings',
                ],
            ]
        ],
        


    ],
]
?>