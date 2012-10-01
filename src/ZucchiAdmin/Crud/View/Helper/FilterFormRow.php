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
use Zucchi\Debug\Debug;


/**
 * Truncate input text
 * 
 * @author Matt Cockayne <matt@zucchi.co.uk>
 * @package ZucchiAdmin
 * @subpackage Crud
 * @category View
 */
class FilterFormRow extends AbstractTranslatorHelper
{
    protected $clauses =  array(
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
    
    protected $options =  array(
        'eq'    => 'Equal to',
        'neq'   => 'Not equal to',
        'gt'    => 'Greater than',
        'gte'   => 'Greater than or equal to',
        'lt'    => 'Less than',
        'lte'   => 'Less than or equal to',
    	'between' => 'Between',
        'fuzzy' => 'Fuzzy',
        'regex' => 'Regular Expression',
    );
    
    
    protected $type =  array(
        'string' => array('fuzzy','regex','eq','neq'),
        'datetime' => array('eq','neq','gt','gte','lt','lte','between'),
        'integer' => array('eq','neq','gt','gte','lt','lte','between'),
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
    public function __invoke($fieldName, $filter, ClassMetadata $metadata)
    {
        $html = '';
        
        if ($metadata->hasField($fieldName)) {
            $html = '
                <div class="control-group">
                    <label class="control-label input-small">' . 
                        ucfirst($this->getView()->filter($fieldName ,'wordcamelcasetoseparator')) . 
                    '</label>';
            
            $field = $metadata->getFieldMapping($fieldName);
            $value = (isset($filter['value'])) ? $filter['value'] : '';
            $operator = (isset($filter['operator'])) ? $filter['operator'] : false;

            switch (strtolower($field['type'])) {
                case "boolean":
                    $el = new Element\Radio();
                    $el->setName('where['.$fieldName.'][value]');
                    $el->setAttribute('options', array('1' => 'Yes','0' => 'No'));
                    $el->setLabelAttributes(array('class' => 'radio inline'));
                    $el->setValue($value ? 1 : 0);
                    
                    $html .= $this->getView()->formRadio($el);
                    break;
                case "integer":
                case "float":
                case "money":
                    $sel = new Element\Select();
                    $sel->setName('where['.$fieldName.'][operator]');
                    $sel->setAttribute('class', 'input-medium query-type');
                    $sel->setAttribute('options', $this->getOptions('integer'));
                    $sel->setValue($operator);
                    $html .= $this->getView()->formSelect($sel);
                    
                    $el = new Element\Number();
                    if (in_array($field['type'], array('float', 'money'))) {
                        $el->setAttribute('step', '0.01');
                    }
                    
                    
                    if (is_array($value)) {
                        $el->setName('where['.$fieldName.'][value][]');
                        $el->setValue($value[0]);
                        $el->setAttributes(array(
                            'class' => 'input-medium',
                            'placeholder' => 'Select Number...',
                        ));
                        $html .= $this->getView()->formNumber($el);
                        $el->setValue($value[1]);
                        $html .= $this->getView()->formNumber($el);
                    } else {
                        $el->setName('where['.$fieldName.'][value]');
                        $el->setValue($value);
                        $el->setAttributes(array(
                            'class' => 'input-large',
                            'placeholder' => 'Select Number...',
                        ));
                        $html .= $this->getView()->formNumber($el);
                    }
                    break;
                case 'datetime';
                    $sel = new Element\Select();
                    $sel->setName('where['.$fieldName.'][operator]');
                    $sel->setAttribute('class', 'input-medium query-type');
                    $sel->setAttribute('options', $this->getOptions('datetime'));
                    $sel->setValue($operator);
                    $html .= $this->getView()->formSelect($sel);
                    
                    $el = new Element\Date();
                    if (is_array($value)) {
                        $el->setName('where['.$fieldName.'][value][]');
                        $el->setValue($value[0]);
                        $el->setAttributes(array(
                            'class' => 'input-medium',
                            'placeholder' => 'Day/Month/Year',
                        ));
                        $html .= $this->getView()->formDate($el);
                        $el->setValue($value[1]);
                        $html .= $this->getView()->formDate($el);
                    } else {
                        $el->setName('where['.$fieldName.'][value]');
                        $el->setValue($value);
                        $el->setAttributes(array(
                            'class' => 'input-large',
                            'placeholder' => 'Day/Month/Year',
                        ));
                        $html .= $this->getView()->formDate($el);
                    }
                    break;
                case 'string';
                default:
                    $sel = new Element\Select();
                    $sel->setName('where['.$fieldName.'][operator]');
                    $sel->setAttribute('class', 'input-medium query-type');
                    $sel->setAttribute('options', $this->getOptions('string'));
                    $sel->setValue($operator);
                    $html .= $this->getView()->formSelect($sel);
//                     
                    $el = new Element\Text();
                    $el->setName('where['.$fieldName.'][value]');
                    $el->setValue($value);
                    $el->setAttributes(array(
                        'class' => 'input-large',
                        'placeholder' => 'Search For...',
                    ));
                    $html .= $this->getView()->formText($el);
                    break;
            }
            
        
            $html .= '
                <button type="button" class="btn btn-warning filter-remove input-small">' . 
                    $this->getTranslator()->translate('Remove') . 
                '</button>
            </div>';
        }
        
        return $html;
    }
    
    /**
     * build a list of options for a select 
     * @param string $type
     * @param string $current
     * return string
     */
    protected function getOptions($type)
    {
        $options = array();
        
        foreach ($this->type[$type] as $t) {
            $options[$t] = $this->options[$t];
        }
        return $options;
    }
    
}
