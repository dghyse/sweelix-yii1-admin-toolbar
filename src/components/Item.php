<?php
/**
 * Item.php
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

use sweelix\yii1\admin\toolbar\components\ItemInterface;
use sweelix\yii1\ext\web\CmsUrlRule;
use CJSON;

/**
 *  Item implements ItemInterface interface
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

class Item implements ItemInterface
{

    /**
     * Array of Items
     *
     * @var array $items
     */
    private $items = [];

    /**
     * Title of item
     *
     * @var string $title
     */
    private $title;

    /**
     * Route of item
     *
     * @var string $route
     */
    private $route;

    /**
     * Type of item
     *
     * @var string $type
     */
    private $type;

    /**
     * Route parameters of item
     *
     * @var array $parameters
     */
    private $parameters;

    /**
     * Fonctionnls options
     *
     * @var array $options
     */
    private $options = [];

    /**
     * Return if is item is authorized
     *
     * @var bool|null $isAuth
     */
    private $isGuest;

    /**
     * This method contruct item
     *
     * @param array $item Item
     */
    public function __construct($item)
    {
        $this->title = (isset($item['title']) === true) ? $item['title']: null;
        $this->route = (isset($item['url']) === true) ? CJSON::decode($item['url']): null;
        $this->type = (isset($item['type']) === true) ? $item['type']: null;
        $this->parameters =  (isset($item['options']) === true) ? CJSON::decode($item['options']): [];
        $this->options =  (isset($item['ItemOptions']) === true) ? CJSON::decode($item['ItemOptions']): [];
        $this->isGuest = (isset($this->options['isGuest']) === true) ? $this->options['isGuest'] : null;
        $this->items = [];
        if (isset($item['items']) === true) {
            foreach($item['items'] as $children) {
                $this->items[] = new self($children);
            }
        }
    }

    /**
     * This methode return children of item
     *
     * @return array
     * @since  XXX
     */
    public function getChildren()
    {
        return $this->items;
    }

    /**
     * This method return title of item
     *
     * @return string
     * @since  XXX
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * This method return route of item
     *
     * @return string
     * @since  XXX
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * This method build url
     *
     * @return mixed|void
     * @since  XXX
     */
    public function getUrl()
    {
    }

    /**
     * This method return type of item
     *
     * @return string
     * @since  XXX
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * This method return route parameters
     *
     * @return array|string
     * @since  XXX
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * This method return item options
     *
     * @return array|string
     * @since  XXX
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * This method return if  his item can use authenticate
     *
     * @return bool
     * @since  XXX
     */
    public function isGuest()
    {
        return $this->isGuest;
    }
}