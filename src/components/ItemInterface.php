<?php
/**
 * ItemInterface.php
 *
 * PHP version 5.4+
 *
 * @author      David Ghyse <dghyse@ibitux.com>
 * @link        http://code.ibitux.net/projects/sweelix
 * @copyright   Copyright 2010-2015 Ibitux
 * @license     http://www.ibitux.com/license license
 * @version     1.3
 * @category    components
 * @package     application.modules.toolbar.components
 */

namespace sweelix\yii1\admin\toolbar\components;

/**
 *  ItemInterface interface
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright Copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   1.3
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  components
 * @package   application.modules.toolbar.components
 * @since     XXX
 */

interface ItemInterface
{
    /**
     * This methode return children of item
     *
     * @return array
     * @since  XXX
     */
    public function getChildren();

    /**
     * This method return title of item
     *
     * @return string
     * @since  XXX
     */
    public function getTitle();

    /**
     * This method return type of url item
     *
     * @return string
     * @since  XXX
     */
    public function getType();

    /**
     * This method return rooute of item
     *
     * @return string
     * @since  XXX
     */
    public function getRoute();

    /**
     * This method build url with route
     *
     * @return mixed
     * @since  XXX
     */
    public function getUrl();

    /**
     * This method return route parameters
     *
     * @return array|string
     * @since  XXX
     */
    public function getParameters();

    /**
     * This method return items options
     *
     * @return array|string
     * @since  XXX
     */
    public function getOptions();
}