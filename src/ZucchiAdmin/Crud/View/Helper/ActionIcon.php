<?php
/**
 * ZucchiAdmin (http://zucchi.co.uk)
 *
 * @link      http://github.com/zucchi/ZucchiAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited. (http://zucchi.co.uk)
 * @license   http://zucchi.co.uk/legals/bsd-license New BSD License
 */
namespace ZucchiAdmin\Crud\View\Helper;

use Zend\I18n\View\Helper\AbstractTranslatorHelper;
use Zend\Form\Element;
use Doctrine\ORM\Mapping\ClassMetadata;
use ZucchiDoctrine\Entity\AbstractEntity;
use Zucchi\Debug\Debug;


/**
 * Truncate input text
 * 
 * @author Matt Cockayne <matt@zucchi.co.uk>
 * @package ZucchiAdmin
 * @subpackage Crud
 * @category View
 */
class ActionIcon extends AbstractTranslatorHelper
{
    /**
     * Truncate input text
     *
     * @param string $action
     * @param int $icon
     * @return string
     */
    public function __invoke($action, $icon, AbstractEntity $entity = null)
    {
        $action = mb_strtolower($action);
        
        $url = $this->getView()->url(null, array('action' => $action));
        
        if ($entity) {
            $url .= '?id=' . $entity->id;
        }
        
        $html = '
        <a class="pull-right action-' . $action . '"
           href="' . $url . '" 
           title="' . $this->getTranslator()->translate(ucfirst($action)) . '"
        >
            <i class="' . $icon . '"></i>&nbsp;
        </a>';
        
        return $html;
    }
    
}
