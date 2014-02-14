<?php
/**
 * UMI.Framework (http://umi-framework.ru/)
 *
 * @link      http://github.com/Umisoft/framework for the canonical source repository
 * @copyright Copyright (c) 2007-2013 Umisoft ltd. (http://umisoft.ru/)
 * @license   http://umi-framework.ru/license/bsd-3 BSD-3 License
 */

namespace utest\hmvc\mock;

use umi\hmvc\component\IComponent;
use umi\hmvc\IMvcEntityFactoryAware;
use umi\hmvc\TMvcEntityFactoryAware;
use umi\route\IRouteAware;
use umi\route\TRouteAware;

/**
 * Class MockComponent
 */
class MockComponent implements IComponent, IRouteAware, IMvcEntityFactoryAware
{
    use TRouteAware;
    use TMvcEntityFactoryAware;

    /**
     * {@inheritdoc}
     */
    public function hasChildComponent($name) {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildComponent($name)
    {
        return $this->createMvcComponent([]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouter()
    {
        return $this->createRouter([]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasController($controllerName) {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getController($controllerName, array $args = []) {}

    /**
     * {@inheritdoc}
     */
    public function getViewRenderer() {}

}