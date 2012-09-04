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
class BulkActions extends AbstractTranslatorHelper
{
    /**
     * default actions
     * @var array
     */
    public $defaultActions = array(
        'export' => 'Export',
        'delete' => 'Delete',
    );
    
    /**
     * Truncate input text
     *
     * @param string $text
     * @param int $length
     * @param bool $wordsafe
     * @param bool $escape
     * @return string
     */
    public function __invoke($actions = null)
    {
        if (!$actions) {
            $actions = $this->defaultActions;
        }
        
        $html = '
        <div id="bulk-actions">
            <strong>' . $this->getTranslator()->translate('Bulk Actions') . '</strong>
            <select>
                <option>Select an action</option>';
        
        foreach ($actions as $action => $label) {
            $html .= '<option value="' . $action . '">' . $label . '</option>';
        }
        
        $html .= '
            </select>
        </div>';
        
        return $html;
    }
    
}
