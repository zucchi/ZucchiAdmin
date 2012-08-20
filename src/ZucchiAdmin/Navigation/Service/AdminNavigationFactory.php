<?php
/**
 * Zucchi (http://zucchi.co.uk/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zucchi Limited (http://zucchi.co.uk)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace ZucchiAdmin\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 * Default navigation factory.
 *
 * @category  Admin
 * @package   ZucchiAdmin
 * @subpackage Navigation
 */
class AdminNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'ZucchiAdmin';
    }
}
