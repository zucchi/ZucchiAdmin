<?php
/**
 * Zucchi (http://zucchi.co.uk)
 *
 * @link      http://github.com/zucchi/ZucchiAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited. (http://zucchi.co.uk)
 * @license   http://zucchi.co.uk/legals/bsd-license New BSD License
 */
namespace ZucchiAdmin\Controller;

use Zucchi\Controller\AbstractController;
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
class AbstractAdminController extends AbstractController
{

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
        
        $where = $this->parseWhere();
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
    
    /**
     * Processes the WHERE clauses and operators provided in the request into
     * a format usable by the getList() method of the services.
     * @return array
     */
    protected function parseWhere()
    {
        $clauses = array(
            'eq'    => '=',
            'gt'    => '>',
            'gte'   => '>=',
            'lt'    => '<',
            'lte'   => '<=',
            'neq'   => '!=',
        	'between' => 'between',
            'fuzzy' => 'like',
            'regex' => 'regexp',
        );
        
        $where = $this->params()->fromQuery('where', array());
        
        // loop through and sanitize the where statement
        foreach ($where as $field => &$value) {
            if (is_array($value)) {
                if (isset($value['value']) && is_string($value['value']) && strlen($value['value'])) {
                    if (isset($value['operator']) && isset($clauses[$value['operator']])) {
                        $value['operator'] = $clauses[$value['operator']];
                    } else {
                        $value['operator'] = '=';
                    }
                }
            } else if (is_string($value) && strlen($value)){
                $value = array(
                    'operator'  => '=',
                    'value'     => $value
                );
            }
        }
        
        return $where;
    }
    
}
