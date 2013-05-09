<?php
/**
 * Zucchi (http://zucchi.co.uk)
 *
 * @link      http://github.com/zucchi/ZucchiAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zucchi Limited. (http://zucchi.co.uk)
 * @license   http://zucchi.co.uk/legals/bsd-license New BSD License
 */
namespace ZucchiAdmin\Controller;

use Zucchi\Controller\AbstractRestController;
use Zucchi\Controller\RequestParserTrait;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\JsonModel;
use Zend\Debug\Debug;


/**
 * Abstract controller to provide base for admin functoinality in Zucchi Modules
 * 
 * @author Matt Cockayne <matt@zucchi.co.uk>
 * @package Zucchi
 * @subpackage Controller
 * @category Components
 */
class AbstractAdminController extends AbstractRestController
{
    use RequestParserTrait;

    /**
     * REST method: Return list of resources
     *
     * @return mixed
     */
    public function getList()
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        
        $service = $this->getServiceLocator()->get($this->service);
        
        $where = $this->parseWhere($this->getRequest());
        $order = $this->params()->fromQuery('order', array());
        
        $list = $service->getList($where, $order);
        
        $model =  new JsonModel(array(
            'list' => $list,
            'where' => $where,
            'order' => $order
        
        ));
        return $model;
    }
    
    /**
     * REST method: Return list of resources
     *
     * @return mixed
     */
    public function get($id)
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        
        $service = $this->getServiceLocator()->get($this->service);
        
        $entity = $service->get($id);
        if (!$entity) {
            throw new \RuntimeException('Entity Not found');
        }
        $model =  new JsonModel($entity->toArray());
        
        return $model;
    }
    
    public function create($data)
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        
        $service = $this->getServiceLocator()->get($this->service);
        
        $entity = $service->getEntity();
        
        $builder = new AnnotationBuilder();
        $form = $builder->createForm($entity);
        $form->bind($entity);

        $form->setData($data);
        if ($form->isValid()) {
            $entity = $service->save($entity);
            $model =  new JsonModel($entity->toArray());
        } else {
            $this->getResponse()->setStatusCode(400);
            $model =  new JsonModel(array(
                'errors' => $form->getMessages(),
            ));
        }
        
        return $model;
    }
    
    public function update($id, $data)
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        
        $service = $this->getServiceLocator()->get($this->service);
        
        $entity = $service->get($id);
        
        $builder = new AnnotationBuilder();
        $form = $builder->createForm($entity);
        $form->bind($entity);

        $form->setData($data);
        if ($form->isValid()) {
            $entity = $service->save($entity);
            $model =  new JsonModel($entity->toArray());
        } else {
            $this->getResponse()->setStatusCode(400);
            $model =  new JsonModel(array(
                'errors' => $form->getMessages(),
            ));
        }
        
        return $model;
    }
    
    public function delete($id)
    {
        if (!$this->service) {
            throw new \RuntimeException('No CRUD service defined');
        }
        $service = $this->getServiceLocator()->get($this->service);
        
        $result = $service->remove($id);
        
        $model =  new JsonModel(array(
            'id' => $id,
             'deleted' => $result,
        ));
        
        return $model;
    }
    
}
