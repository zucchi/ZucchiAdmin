<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'zucchi-admin-dashboard' => 'ZucchiAdmin\Controller\DashboardController',
        ),
    ),
    'navigation' => array(
        'ZucchiAdmin' => array(
            'dashoard' => array(
                'label' => 'Dashboard',
                'route' => 'ZucchiAdmin',
            ),
        )
    ),
    'service_manager' => array(
       'factories' => array(
           'zucchiadmin.navigation' => '\ZucchiAdmin\Navigation\Service\AdminNavigationFactory',
       ),
    ),
    'router' => array(
        'routes' => array(
            'ZucchiAdmin' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/admin',
                    'defaults' => array(
                        'controller' => 'zucchi-admin-dashboard',
                    ),
                ),
                'priority' => 999999, // high priority to enforce admin urls not easily over-ridden
                'may_terminate' => true,
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ZucchiAdmin' => __DIR__ . '/../view',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
