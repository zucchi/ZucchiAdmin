<?php
/**
 * Zucchi (http://zucchi.co.uk/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited (http://zucchi.co.uk)
 * @license   http://zucchi.co.uk/legals/bsd-license New BSD License
 * @package   Zend_Navigation
 */

namespace ZucchiAdmin\Navigation\Service;

use ZendTest\Stdlib\SplPriorityQueueTest;

use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\Config;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Navigation\Exception;
use Zend\Navigation\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Default navigation factory.
 *
 * @category  Admin
 * @package   ZucchiAdmin
 * @subpackage Navigation
 */
class AdminNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'ZucchiAdmin';
    }
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array
     * @throws \Zend\Navigation\Exception\InvalidArgumentException
     */
    protected function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $configuration = $serviceLocator->get('Configuration');

            if (!isset($configuration['navigation'])) {
                throw new Exception\InvalidArgumentException('Could not find navigation configuration key');
            }
            if (!isset($configuration['navigation'][$this->getName()])) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Failed to find a navigation container by the name "%s"',
                    $this->getName()
                ));
            }

            $application = $serviceLocator->get('Application');
            $permService = $serviceLocator->get('zucchisecurity.perm');
            $routeMatch  = $application->getMvcEvent()->getRouteMatch();
            $router      = $application->getMvcEvent()->getRouter();
            $pages       = $this->getPagesFromConfig($configuration['navigation'][$this->getName()]);

            $this->pages = $this->injectComponents($pages, $routeMatch, $router, $permService);
        }
        return $this->pages;
    }
    
    /**
     * @param array $pages
     * @param RouteMatch $routeMatch
     * @param Router $router
     * @return mixed
     */
    protected function injectComponents(array $pages, RouteMatch $routeMatch = null, Router $router = null, $permService = null)
    {
        foreach ($pages as &$page) {
            $hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['route']);
            if ($hasMvc) {
                if (!isset($page['routeMatch']) && $routeMatch) {
                    $page['routeMatch'] = $routeMatch;
                }
                if (!isset($page['router'])) {
                    $page['router'] = $router;
                }
            }
            
            if (isset($page['route']) && $permService && !$permService->can('view', 'route:' . $page['route'])) {
                $page['visible'] = false;
            }

            if (isset($page['pages'])) {
                $page['pages'] = $this->injectComponents($page['pages'], $routeMatch, $router, $permService);
            }
        }
        return $pages;
    }
}
