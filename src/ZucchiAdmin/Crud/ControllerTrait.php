<?php
/**
 * Zucchi (http://zucchi.co.uk)
 *
 * @link      http://github.com/zucchi/ZucchiAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited. (http://zucchi.co.uk)
 * @license   http://zucchi.co.uk/legals/bsd-license New BSD License
 */
namespace ZucchiAdmin\Crud;

use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\Fieldset;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;
use Zucchi\Debug\Debug;
use Zucchi\Form\Factory as FormFactory;

use ZucchiAdmin\Crud\Event\CrudEvent;

use ZucchiDoctrine\Hydrator\DoctrineEntity as DoctrineHydrator;

/**
 * Trait to facilitate default CRUD implementation
 * 
 * @author Matt Cockayne <matt@zucchi.co.uk>
 * @package ZucchiAdmin
 * @subpackage Controller
 * @category CRUD
 */
trait ControllerTrait
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

        $label = (isset($this->label)) ? $this->label : '';
        
        $service = $this->getServiceLocator()->get($this->service);

        $this->trigger(CrudEvent::EVENT_LIST_PRE);

        $where = $this->parseWhere($this->getRequest());
        $order = $this->params()->fromQuery('order', array());
        
        $list = $service->getList($where, $order);

        $this->trigger(CrudEvent::EVENT_LIST_POST, $list);
        
        return $this->loadView(
            'zucchi-admin/crud/list', 
            array(
                'list' => $list,
                'listFields' => $this->listFields,
                'metadata' => $service->getMetaData(),
                'where' => $where,
                'order' => $order,
                'label' => $label,

            )
        );
    }
    
    /**
     * Action to handle creation of entity
     */
    public function createAction()
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }

        $label = (isset($this->label)) ? $this->label : '';
        
        $service = $this->getServiceLocator()->get($this->service);

        $this->trigger(CrudEvent::EVENT_CREATE_PRE);

        $entity = $service->getEntity();

        $builder = new AnnotationBuilder();
        $formFactory = new FormFactory();
        $formFactory->setServiceManager($this->getServiceLocator());
        $builder->setFormFactory($formFactory);
        $form = $builder->createForm($entity);
        
        $this->getEventManager()->trigger('crud.create.form', $form);
        
        $form->bind($entity);

        if ($this->request->isPost()) {
             $form->setData($this->request->getPost());
             if ($form->isValid()) {
                 $entity = $service->save($entity);
                 $this->flashMessenger()->addMessage(array(
                    'message'     => sprintf('%1$s sucessfully saved', $label),
                    'status'      => 'success',
                    'title'       => 'Saved',
                    'dismissable' => true
                 ));

                 $this->trigger(CrudEvent::EVENT_CREATE_POST, $form);

                 $this->redirect()->toRoute(null, array('action'=>'list'));
             }
         }
        
        $this->addFormActions($form);

        return $this->loadView(
            'zucchi-admin/crud/create',
            array(
                'form' => $form,
                'label' => $label,
            )
        );
    }
    
    /**
     * Action to handle editing of entity
     */
    public function updateAction()
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        
        if (!$id = $this->params()->fromQuery('id', false)) {
            throw new \RuntimeException('you must specifiy the id you wish to update');
        }

        $label = (isset($this->label)) ? $this->label : '';
        
        $service = $this->getServiceLocator()->get($this->service);

        $this->trigger(CrudEvent::EVENT_UPDATE_PRE);
        
        $entity = $service->get($id);

        if (!$entity) {
            throw new \RuntimeException('We could not find the item you want to update');
        }

        $builder = new AnnotationBuilder();
        $formFactory = new FormFactory();
        $formFactory->setServiceManager($this->getServiceLocator());
        $builder->setFormFactory($formFactory);
        $form = $builder->createForm($entity);

        $this->getEventManager()->trigger('crud.update.form', $form);

        $form->bind($entity);

        if ($this->request->isPost()) {
             $form->setData($this->request->getPost());
             if ($form->isValid()) {
                 $entity = $service->save($entity);
                 $this->flashMessenger()->addMessage(array(
                    'message'     => sprintf('%1$s sucessfully saved', $label),
                    'status'      => 'success',
                    'title'       => 'Saved',
                    'dismissable' => true
                 ));
                 $this->trigger(CrudEvent::EVENT_UPDATE_POST, $form);

                 $this->redirect()->toRoute(null, array('action'=>'list'));
             } else {
                 $this->messenger()->addMessage(
                     sprintf('Failed to save %1$s', $label),
                     'error',
                     'Failure',
                     true
                 );
             }
         }
        
        $this->addFormActions($form);

        return $this->loadView(
            'zucchi-admin/crud/update',
            array(
                'form' => $form,
                'label' => $label,
            )
        );
    }
    
    /**
     * Action to handle deletion of entity
     */
    public function deleteAction()
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }

        $label = (isset($this->label)) ? $this->label : '';
        
        if ($this->params()->fromQuery('confirmed', false) == 'delete') {
            // if delete has been confirmed
            $service = $this->getServiceLocator()->get($this->service);

            $this->trigger(CrudEvent::EVENT_DELETE_PRE, $service);
            
            $result = $service->remove($this->params()->fromQuery('id', null));

            $this->flashMessenger()->addMessage(array(
                    'message'     => sprintf('%1$s x %2$s successfully deleted', $result, $label),
                    'status'      => ($result) ? 'success' : 'warning',
                    'title'       => 'Deleted',
                    'dismissable' => true
            ));
            $this->trigger(CrudEvent::EVENT_DELETE_POST, $result);

            $this->redirect()->toRoute(null, array('action'=>'list'));
        }

        return $this->loadView(
            'zucchi-admin/crud/delete',
            array(
                'queryString' => http_build_query($this->params()->fromQuery()),
                'label' => $label,
            )
        );
    }
    
    public function exportAction()
    {
        $this->trigger(CrudEvent::EVENT_EXPORT_PRE);
        $this->trigger(CrudEvent::EVENT_EXPORT_POST);
        return $this->loadView('zucchi-admin/crud/export');
    }
    
    
    protected function addFormActions($form)
    {
        $actions = new Fieldset('actions');
        $actions->setAttribute('class', 'form-actions');
        
        $actions->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Save',
                'class' => 'btn btn-primary'
            ),
            'options' => array(
                'bootstrap' => array(
                    'style' => 'inline',
                ),
            ),
        ));
        
        $actions->add(array(
            'name' => 'reset',
            'attributes' => array(
                'type' => 'reset',
                'value' => 'reset',
                'class' => 'btn'
            ),
            'options' => array(
                'bootstrap' => array(
                    'style' => 'inline',
                ),
            ),
        ));
        $form->add($actions, array('priority' => -9999));
        return $form;
    }

    public function trigger($name, $target = null)
    {
        $event = new CrudEvent();
        $event->setRequest($this->getRequest())
            ->setResponse($this->getResponse())
            ->setServiceManager($this->getServiceLocator());

        if ($target) {
            $event->setTarget($target);
        }

        $eventManager = $this->getEventManager();
        $eventManager->trigger($name, $event);
    }
}