<?php
/**
 * Toolbar.php
 *
 * PHP version 5.4+
 *
 * @author      David Ghyse <dghyse@ibitux.com>
 * @link        http://code.ibitux.net/projects/sweelix
 * @copyright   Copyright 2010-2015 Ibitux
 * @license     http://www.ibitux.com/license license
 * @version     1.4
 * @category    components
 * @package     application.modules.toolbar.components
 */

namespace sweelix\yii1\admin\toolbar\components;

use sweelix\yii1\admin\toolbar\models\Toolbar as ToolbarModel;
use sweelix\yii1\admin\toolbar\components\Item;
use Yii;

/**
 *  Coomposent Toolbar
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright Copyright 2010-2014 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  components
 * @package   application.modules.toolbar.components
 * @since     1.2
 */
class Toolbar
{
    /**
     * Array of Item
     * @var array $toolBarItems
     */
    private  $toolBarItems;

    /**
     * Id of this
     *
     * @var string $toolbarId
     */
    private  $toolbarId;

    /**
     * This method get instance
     *
     * @param string $name
     *
     * @throws \Exception
     *
     * @since  1.4
     */
    public  function __construct($name) {
        try {
            \Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\components\Toolbar');
            $items = [];
            $toolbar = ToolbarModel::model()->findByAttributes(['toolbarName' => $name, 'toolbarIsActive' => true]);
            if ($toolbar !== null) {
                $itemsData = \CJSON::decode($toolbar->toolbarCore);
                $this->toolbarId = md5($toolbar->toolbarId.$toolbar->toolbarDateUpdate);
                if ($itemsData !== null) {
                    foreach($itemsData as $item) {
                        $newItem = new Item($item);
                        $isGuest = $newItem->isGuest();
                        if ( ($isGuest !== null) ) {
                            if (($isGuest === Yii::app()->user->isGuest)) {
                                $items[] = $newItem;
                            }
                        } else {
                            $items[] = $newItem;
                        }
                    }
                }
            }
            $this->toolBarItems = $items;

        } catch(\Exception $e) {
            \Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\components\Toolbar');
            throw $e;
        }
    }


    /**
     * This method return items
     *
     * @return array
     * @throws \Exception
     * @return \Array
     * @since  XXX
     */
    public function getToolbar()
    {
        try {
            \Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\components\Toolbar');
            return $this->toolBarItems;
        } catch(\Exception $e) {
            \Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\components\Toolbar');
            throw $e;
        }
    }

    /**
     * This method get id
     *
     * @return mixed
     * @since  1.4
     */
    public  function getId()
    {
        return $this->toolbarId;
    }
}