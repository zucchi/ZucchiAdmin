<?php
/**
 * ZucchiAdmin (http://zucchi.co.uk)
 *
 * @link      http://github.com/zucchi/ZucchiAdmin for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zucchi Limited. (http://zucchi.co.uk)
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
class BulkCheckbox extends AbstractTranslatorHelper
{
    /**
     * Truncate input text
     *
     * @param string $text
     * @param int $length
     * @param bool $wordsafe
     * @param bool $escape
     * @return string
     */
    public function __invoke($id)
    {
        $html = '';
        if ($id instanceof AbstractEntity) {
            $id = $id->id;
        }
        
        if ($id) {
            $html = '
            <input class="pull-right bulk-action" 
                   type="checkbox" 
                   id="bulk-action-' . $id . '" 
                   value="' . $id . '" />';
        }
        
        return $html;
    }
    
}
