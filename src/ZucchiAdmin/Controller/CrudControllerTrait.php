<?php
/**
 * Zucchi (http://zucchi.co.uk)
 *
 * @link      http://github.com/zucchi/ZucchiAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited. (http://zucchi.co.uk)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZucchiAdmin\Controller;

/**
 * Trait to facilitate default CRUD implementation
 * 
 * @author Matt Cockayne <matt@zucchi.co.uk>
 * @package ZucchiAdmin
 * @subpackage Controller
 * @category CRUD
 */
trait CrudControllerTrait
{
    /**
     * Action to list available entities 
     * 
     * @throws \RuntimeException
     * @return ViewModel
     */
    public function listAction()
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        
        $service = $this->getServiceLocator()->get($this->service);
        
        $service->get();
        
        return $this->loadView('zucchi-admin/crud/list');
    }
    
    /**
     * Action to handle creation of entity
     */
    public function createAction()
    {
        return $this->loadView('zucchi-admin/crud/create');
    }
    
    /**
     * Action to handle editing of entity
     */
    public function editAction()
    {
        return $this->loadView('zucchi-admin/crud/edit');
    }
    
    /**
     * Action to handle deletion of entity
     */
    public function deleteAction()
    {
        return $this->loadView('zucchi-admin/crud/delete');
    }
    
}