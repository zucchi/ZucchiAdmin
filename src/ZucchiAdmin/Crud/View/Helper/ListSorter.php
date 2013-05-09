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
use Zend\Form\Element;
use Doctrine\ORM\Mapping\ClassMetadata;
use Zucchi\Debug\Debug;


/**
 * Truncate input text
 * 
 * @author Matt Cockayne <matt@zucchi.co.uk>
 * @package ZucchiAdmin
 * @subpackage Crud
 * @category View
 */
class ListSorter extends AbstractTranslatorHelper
{
    protected $query = array();
    
    
    /**
     * Truncate input text
     *
     * @param string $text
     * @param int $length
     * @param bool $wordsafe
     * @param bool $escape
     * @return string
     */
    public function __invoke($field, $label = null)
    {
        
        if (!$label) $label = $field;
        
        parse_str($_SERVER['QUERY_STRING'], $this->query);
        
        if (isset($this->query['order'][$field])) { 
            switch (strtolower($this->query['order'][$field])) {
                case 'desc':
                    $clear = true;
                    $class = 'icon-arrow-down';
                    $this->query['order'][$field] = 'asc';
                    break;
                case 'asc':
                    $clear = true;
                    $class = 'icon-arrow-up';
                    $this->query['order'][$field] = 'desc';
                    break;
            }
        } else {
            $clear = false;
            $class = 'icon-resize-vertical';
            $this->query['order'][$field] = 'asc';
        } 
        
        $translator = $this->getTranslator();
        $html = '
            <a href="?' . http_build_query($this->query) . '" class="sorter pull-right" title="' . $translator->translate('Sort by') .' ' . $label . '">
                <i class="' . $class . '"></i>
            </a>
        ';
        if ($clear) {
            unset($this->query['order'][$field]);
            $html .= '
                <a href="?' . http_build_query($this->query) . '" class="sorter pull-right" title="' . $translator->translate('Remove sort by') .' ' . $label . '">
                    <i class="icon-remove"></i>
                </a>
            ';
        }
        
        return $html;
    }
    
}
