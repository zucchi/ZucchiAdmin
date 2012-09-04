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
                        'action' => 'index',
                    ),
                ),
                'priority' => 999999, // high priority to enforce admin urls not easily over-ridden
                'may_terminate' => true,
            ),
        ),
    ),
    'translator' => array(
        'locale' => 'en_GB',
        'translation_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'crudfilterformrow' => 'ZucchiAdmin\Crud\View\Helper\FilterFormRow',
            'crudlistsorter' => 'ZucchiAdmin\Crud\View\Helper\ListSorter',
            'crudactionicon' => 'ZucchiAdmin\Crud\View\Helper\ActionIcon',
            'crudbulkcheckbox' => 'ZucchiAdmin\Crud\View\Helper\BulkCheckbox',
            'crudbulkactions' => 'ZucchiAdmin\Crud\View\Helper\BulkActions',
        ),
    ),
);
