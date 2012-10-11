<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZucchiAdmin\Crud\Event;

use Zend\EventManager\Event;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\ModelInterface as Model;
use Zend\View\Model\ViewModel;
use Zucchi\ServiceManager\ServiceManagerAwareTrait;

/**
 * @category   Zend
 * @package    Zend_Mvc
 */
class CrudEvent extends Event
{
    use ServiceManagerAwareTrait;

    /**#@+
     * Security events triggered by eventmanager
     */
    const EVENT_LIST_PRE     = 'zucchicrud.list.pre';
    const EVENT_LIST_POST    = 'zucchicrud.list.post';
    const EVENT_CREATE_PRE   = 'zucchicrud.create.pre';
    const EVENT_CREATE_POST  = 'zucchicrud.create.post';
    const EVENT_UPDATE_PRE   = 'zucchicrud.update.pre';
    const EVENT_UPDATE_POST  = 'zucchicrud.update.post';
    const EVENT_DELETE_PRE   = 'zucchicrud.delete.pre';
    const EVENT_DELETE_POST  = 'zucchicrud.update.post';
    const EVENT_EXPORT_PRE   = 'zucchicrud.export.pre';
    const EVENT_EXPORT_POST  = 'zucchicrud.export.post';
    /**#@-*/

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param Request $request
     * @return MvcEvent
     */
    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;
        return $this;
    }

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param Response $response
     * @return MvcEvent
     */
    public function setResponse(Response $response)
    {
        $this->setParam('response', $response);
        $this->response = $response;
        return $this;
    }
}
