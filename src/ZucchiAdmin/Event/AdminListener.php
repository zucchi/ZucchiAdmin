<?php
/**
 * ZucchiLayout (http://zucchi.co.uk/)
 *
 * @link      http://github.com/zucchi/ZucchiLayout for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zucchi Limited (http://zucchi.co.uk)
 * @license   http://zucchi.co.uk/legals/bsd-license New BSD License
 */

namespace ZucchiAdmin\Event;

use Zend\EventManager\Event;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZucchiAdmin\Controller\AbstractAdminController;
use Zucchi\Debug\Debug;

/**
 * Strategy for allowing manipulation of layout 
 * 
 * @category   Layout
 * @package    ZucchiLayout
 * @subpackage Layout
 */
class AdminListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * flag to mark if the current request is an admin request
     * @var bool
     */
    protected $isAdmin = false;
    
    
    /**
     * Attach the a listener to the specified event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners = array(
            $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'testAdmin'), -9999),
            $events->attach(MvcEvent::EVENT_RENDER, array($this, 'applyLayout'), -9999),
        );
    }
    
    /**
     * remove listeners from events
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        array_walk($this->listeners, array($events,'detach'));
        $this->listeners = array();
    }
    
    /**
     * test if admin controller is being loaded
     * 
     * @param MvcEvent $e
     */
    public function testAdmin(MvcEvent $e)
    {
        $controller = $e->getTarget();
        if ($controller instanceof AbstractAdminController) {
            $this->isAdmin = true;
        }
    }

    /**
     * prepare the layour
     *
     * @param  MvcEvent $e
     * @return Response
     */
    public function applyLayout(MvcEvent $e)
    {
        if ($this->isAdmin) {
            $em = $e->getApplication()->getEventManager();
            
            $viewModel = $e->getViewModel();
            if (!$viewModel->terminate()) {
                $viewModel->setTemplate('zucchi-admin/layout');
            }
        }
        
    }
}
