<?php
/**
 * ZucchiLayout (http://zucchi.co.uk/)
 *
 * @link      http://github.com/zucchi/ZucchiLayout for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited (http://zucchi.co.uk)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZucchiAdmin\Event;

use Zend\EventManager\Event;

use Zend\EventManager\SharedEventManagerInterface;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Navigation\Navigation;

use ZucchiAdmin\Controller\AbstractAdminController;

use Zucchi\Debug\Debug;

/**
 * Strategy for allowing manipulation of layout 
 * 
 * @category   Layout
 * @package    ZucchiLayout
 * @subpackage Layout
 */
class AdminListener
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
    public function attach(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'application',
            MvcEvent::EVENT_DISPATCH, 
            array($this, 'testAdmin'),
            -9999
        );
        
        $this->listeners[] = $events->attach(
            'application',
            MvcEvent::EVENT_RENDER, 
            array($this, 'applyLayout'),
            -9999
        );
    }
    
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
            $viewModel->setTemplate('zucchi-admin/layout');
        }
        
    }
}
