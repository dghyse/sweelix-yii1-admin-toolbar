<?php
/**
 *  ToolbarController.php
 *
 *  PHP version 5.4+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/licence licence
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  controllers
 * @package   application.controllers
 */

namespace sweelix\yii1\admin\toolbar\controllers;

use sweelix\yii1\admin\toolbar\components\Html;
use sweelix\yii1\admin\toolbar\models\Search;
use sweelix\yii1\admin\toolbar\models\Toolbar;
use sweelix\yii1\admin\core\web\Controller;
use sweelix\yii1\ext\components\RouteEncoder;
use sweelix\yii1\ext\db\CriteriaBuilder;
use CActiveDataProvider;
use CDbCriteria;
use CJSON;
use CClientScript;
use CLogger;
use Exception;
use Yii;

/**
 * this class is used to manage toolbar
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/licence licence
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  controllers
 * @package   application.controllers
 */

class ToolbarController extends Controller
{

    /**
     * This method render index
     *
     * @throws Exception
     * @return void
     * @since  1.2
     */
    public function actionIndex($page = 0)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');

            //Init params
            $toolbar = new Toolbar('createName');
            $success = false;
            $toolbarData = null;
            $criteria = new \CDbCriteria();
            $criteria->order = 'toolbarName ASC';
            $toolbarIsDescribe = false;


            //Get post
            $post = Yii::app()->getRequest()->getPost(Html::modelName($toolbar), null);


            //Test if exists
            if ($post !== null) {

                //Get attributes into POST
                $toolbar->attributes = $post;

                //Init date create
                $toolbar->toolbarDateCreate = date('Y-m-d H:i:s');
                //Validate data
                if ($toolbar->validate() === true) {
                    $success = true;
                    if (empty($toolbar->toolbarTmpDescription) === false) {
                        $success = $success && $this->generate($toolbarData, $toolbar);
                        $toolbarIsDescribe = true;
                    }
                    if ($success === true) {
                        if (empty($toolbarData) === false) {
                            $toolbar->toolbarCore = CJSON::encode($toolbarData);
                            if ($toolbarIsDescribe === true) {
                                $toolbar->toolbarTextDescription = CJSON::encode($toolbar->toolbarTmpDescription);
                            }

                        }
                        $toolbar->save(false);
                    }
                }

                //Save data if it's good
                if ($success === true) {
                    $this->redirect(['toolbar/update', 'toolbarId' => $toolbar->toolbarId]);
                }
            }

            //Find all toolbar and create active data provider
            $activeDataToolbars = new \CActiveDataProvider(
                $toolbar,
                [
                    'criteria' => $criteria,
                    'pagination' => [
                        'pageSize' => $this->getModule()->pagerSize,
                        'currentPage' => $page,
                    ],
                ]
            );

            //Render view
            if (Yii::app()->getRequest()->isAjaxRequest === true) {
                $this->renderPartial(
                    '_index',
                    [
                        'toolbar' => $toolbar,
                        'activeDataToolbars' => $activeDataToolbars,
                    ]
                );
            } else {
                $this->render(
                    'index',
                    [
                        'toolbar' => $toolbar,
                        'activeDataToolbars' => $activeDataToolbars,
                    ]
                );
            }
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method update toolbar
     *
     * @param null $toolbarId
     * @param null $indexItem
     *
     * @throws Exception
     * @throws \CHttpException
     * @since  XXX
     */
    public function actionUpdate($toolbarId = null, $indexItem = null)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            //Init params
            $toolbar = null;
            $success = false;
            $recomputeSuccess = false;
            $toolbarData = [];
            if ($toolbarId !== null) {
                $toolbar = Toolbar::model()->findByPk($toolbarId);
            }
            //Find toolbar and get core and description
            if ($toolbar === null) {
                throw new \CHttpException('400', 'Toolbar not found');
            }

            if ($toolbar->toolbarTextDescription !== null) {
                $toolbar->toolbarTmpDescription = CJSON::decode($toolbar->toolbarTextDescription);
            }
            //Update scenario
            $toolbar->scenario = 'update';
            //Get post
            $post = Yii::app()->getRequest()->getPost(Html::modelName($toolbar), null);
            //Get Adding button
            $addingButton = Yii::app()->getRequest()->getPost('Adding', null);
            //Get Recompute button
            $recomputeButton = Yii::app()->getRequest()->getPost('Recompute', null);
            //Get index item
            if (($addingButton !== null) && ($post === null)) {
                $toolbar->toolbarNewItemAction = Toolbar::PUT_TOOLBAR;
            }

            //Test if post exists
            if ($post !== null) {
                //Get attributes into POST
                $toolbar->attributes = $post;
                if ($recomputeButton !== null) {
                    $recomputeSuccess = $this->generate($toolbarData, $toolbar);
                    if ($recomputeSuccess === true) {
                        $toolbar->toolbarCore = CJSON::encode($toolbarData);
                        $toolbar->toolbarTextDescription = CJSON::encode($toolbar->toolbarTmpDescription);
                        $toolbar->save(false);
                    }
                } else {
                    $success = $this->saveItem($toolbar, $indexItem);
                }
            }

            //Build toolbar
            $dataJsonDecode = CJSON::decode($toolbar->toolbarCore);
            $toolbarItems = ($dataJsonDecode !== null) ? $dataJsonDecode : [];
            if ($recomputeSuccess === true) {
                $textRebuild = $this->buildTextDescription($toolbarItems, 1);
                $toolbar->toolbarTextDescription = CJSON::encode($textRebuild);
                $toolbar->save(false);
                $toolbar->toolbarTmpDescription = $textRebuild;
            }
            $toolbarBuilding = $this->build($toolbarItems, $toolbarId, $indexItem, null, true);
            //Render view
            if ($success === false) {
                if (Yii::app()->getRequest()->isAjaxRequest === true) {
                    $this->renderPartial(
                        '_update',
                        [
                            'toolbar' => $toolbar,
                            'toolbarBuilding' => $toolbarBuilding,
                            'indexItem' => $indexItem,
                        ]
                    );
                } else {
                    $this->render(
                        'update',
                        [
                            'toolbar' => $toolbar,
                            'toolbarBuilding' => $toolbarBuilding,
                            'indexItem' => $indexItem,
                        ]
                    );
                }
            } else {
                $js = '
                    jQuery("#view-delete").parent().hide(
                        "fast",
                        function(){jQuery(this).children().html("");
                    });
                    ';
                $this->renderJs($js, true);
            }

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }


    /**
     * This method load item in toolbar
     *
     * @param integer $toolbarId Id of toolbar
     * @param string  $indexItem Index of item in toolbar
     *
     * @throws Exception
     * @return void
     * @since  1.4
     */
    public function actionLoad($toolbarId, $indexItem)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            //Get toolbar
            $toolbar = Toolbar::model()->findByPk($toolbarId);
            //Get toolbar Core
            if ($toolbar !== null) {
                $dataJsonDecode = CJSON::decode($toolbar->toolbarCore);
                $toolbarItems = ($dataJsonDecode !== null) ? $dataJsonDecode : [];
                $toolbarIndex = preg_split('/(_)/', $indexItem);
                //Get Item
                $toolbarData = $this->manageItem($toolbarItems, null, $toolbarIndex);
                $url = $toolbarData['url'];
                if (is_array($toolbarData['url']) === true) {
                    $url = CJSON::encode($toolbarData['url']);
                }
                //Init toolbar
                $toolbar->toolbarHtml = (isset($toolbarData['title']) === true) ?$toolbarData['title']:null;
                $toolbar->toolbarUrl = $url;
                $toolbar->toolbarUrlType = (isset($toolbarData['type']) === true) ?$toolbarData['type']:null;
                $toolbar->toolbarHtmlOptions = (isset($toolbarData['options']) === true) ?$toolbarData['options']:null;
                $toolbar->toolbarItemFonctOptions = (isset($toolbarData['ItemOptions']) === true) ?$toolbarData['ItemOptions']:null;
                $toolbar->toolbarNewItemAction = Toolbar::UPDATE_TOOLBAR;
            } else {
                $toolbar = new Toolbar();
            }
            //Render
            $this->renderPartial(
                '_update',
                [
                    'toolbar' => $toolbar,
                    'indexItem' => $indexItem,
                ]
            );

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method move item under array toolbar
     *
     * @param integer $toolbarId Id of toolbar
     *
     * @throws Exception
     * @return void
     * @since  1.2
     */
    public function actionMove($toolbarId = null)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            $toolbarItems = [];
            $toolbar = null;
            if(\Yii::app()->request->isAjaxRequest === true) {
                if ($toolbarId !== null) {
                    $toolbar = Toolbar::model()->findByPk($toolbarId);
                }

                if ($toolbar !== null) {
                    $dataJsonDecode = CJSON::decode($toolbar->toolbarCore);
                    $toolbarItems = ($dataJsonDecode !== null) ? $dataJsonDecode : [];
                }
                //Get source and target in request
                $sourceIndex = Yii::app()->getRequest()->getParam('sourceIndex');
                $targetIndex = Yii::app()->getRequest()->getParam('targetIndex');
                $moveMode = Yii::app()->getRequest()->getParam('target');

                //Find source item
                $itemSource = $this->manageItem($toolbarItems, null, preg_split('/(_)/', $sourceIndex));

                //Create target wire
                $targetWire = preg_split('/(_)/', $targetIndex);

                //Check move mode
                if (($moveMode === Toolbar::TARGET_AFTER) || ($moveMode === Toolbar::TARGET_BEFORE)) {
                    $tmp = preg_split('/(_)/', $targetIndex);
                    if (count($tmp) > 1) {
                        $targetWire = array_slice($tmp, 0, count($tmp) - 1);
                    }
                }

                if ($targetIndex !== null) {
                    //Put value to target
                    $this->manageItem($toolbarItems, $itemSource, $targetWire, Toolbar::PUT_TOOLBAR);
                    //Delete old value
                    $this->manageItem($toolbarItems, null, preg_split('/(_)/', $sourceIndex), Toolbar::DELETE_TOOLBAR);
                }
                //Re index array
                $indexedToolbar = $this->reIndexArray($toolbarItems);

                //Save data
                $toolbar->toolbarCore = CJSON::encode($indexedToolbar);
                $textRebuild = $this->buildTextDescription($indexedToolbar);
                $toolbar->toolbarTextDescription = CJSON::encode($textRebuild);
                $toolbar->toolbarTmpDescription = $textRebuild;
                $toolbar->saveAttributes(['toolbarCore', 'toolbarTextDescription']);

                //Build new toolbar
                $toolbarBuilding = $this->build($toolbarItems, $toolbarId, $sourceIndex, null, true);

                //Render view
                $this->renderPartial(
                    '_toolbar',
                    [
                        'toolbarBuilding' => $toolbarBuilding,
                        'toolbar' => $toolbar,
                        'indexItem' => null
                    ]
                );
            }
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }


    /**
     * This method delete toolbar
     *
     * @param integer $toolbarId  Id of toolbar
     * @param integer $page       Cuurent page
     *
     * @throws Exception
     * @return void
     * @since  1.4
     */
    public function actionDelete($toolbarId, $page)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');

            $success = false;
            $htmlTargetIds = [
                'target' => '#listToolbar',
                'untarget' => '#view-delete',
            ];

            //Get toolbar
            $toolbar = Toolbar::model()->findByPk($toolbarId);
            //Get post
            $post = Yii::app()->getRequest()->getPost('response');
            if ($post !== null) {
                //Check response
                if ($post === 'yes') {
                    $success = $toolbar->delete();
                } else {
                    $success = null;
                }
            }

            //Render
            if ($success !== true) {
                $this->renderPartial(
                    '_validate',
                    [
                        'htmlTargetIds' => $htmlTargetIds,
                    ]
                );
            } else {
                if (Yii::app()->getRequest()->isAjaxRequest === true) {
                    //Create criteria
                    $criteria = new CDbCriteria();
                    $criteria->order = 'toolbarName ASC';
                    //Find all toolbar and create active data provider
                    $activeDataToolbars = new CActiveDataProvider(
                        $toolbar,
                        [
                            'criteria' => $criteria,
                            'pagination' => [
                                'pageSize' => $this->getModule()->pagerSize,
                                'currentPage' => $page,
                            ],
                        ]
                    );
                    $this->renderPartial(
                        '_index',
                        [
                            'toolbar' => $toolbar,
                            'activeDataToolbars' => $activeDataToolbars
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }



    /**
     * This method refresh nav toolbar menu
     *
     * @param interger $toolbarId    Id of toolbar
     * @param string   $currentIndex Current index
     *
     * @throws Exception
     * @return void
     * @since  1.2
     */
    public function actionRefresh($toolbarId, $currentIndex = null)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');

            //Get toolbar
            $toolbar = Toolbar::model()->findByPk($toolbarId);
            $toolbarItems = [];
            //Get toolbar Core
            if ($toolbar !== null) {
                $dataJsonDecode = CJSON::decode($toolbar->toolbarCore);
                $toolbarItems = ($dataJsonDecode !== null) ? $dataJsonDecode : [];
            }

            //Build toolbar
            $toolbarBuilding = $this->build($toolbarItems, $toolbarId, $currentIndex, null, true);
            $toolbar->toolbarTmpDescription = CJSON::decode($toolbar->toolbarTextDescription);

            //Render
            $this->renderPartial(
                '_toolbar',
                [
                    'toolbarBuilding' => $toolbarBuilding,
                    'toolbar' => $toolbar,
                    'indexItem' => $currentIndex
                ]
            );

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method activate toolbar
     *
     * @param $toolbarId      Id of toolbar
     * @param string $action  Action to do
     *
     * @throws Exception
     * @return void
     * @since  1.2
     *
     */
    public function actionActivate($toolbarId, $action = 'activate', $page = 0)
    {
        try {
            Yii::trace('Trace: ' . __CLASS__ . '::' . __FUNCTION__ . '()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            $success = true;
            //Get current toolbar
            $toolbar = Toolbar::model()->findByPk($toolbarId);
            //If toolbar exists
            if ($toolbar !== null) {
                if ($action === 'activate') {
                    $toolbar->toolbarIsActive = true;
                } else {
                    $toolbar->toolbarIsActive = false;
                }
                $toolbar->saveAttributes(['toolbarIsActive']);
            } else {
                $success = false;
            }
            //If mistakes
            if ($success === false) {
                //Render error message
                if (Yii::app()->getRequest()->isAjaxRequest === true) {
                    $js = Html::raiseShowNotice(
                        [
                            'title' => Yii::t('toolbar', 'Important'),
                            'text' => Yii::t('toolbar', 'Toolbar not exists'),
                            'cssClass' => 'warning',
                        ]
                    );
                    $this->renderJs($js);
                }
            } else {
                //Render new toolbar list
                if (Yii::app()->getRequest()->isAjaxRequest === true) {
                    //Create criteria
                    $criteria = new CDbCriteria();
                    $criteria->order = 'toolbarName ASC';
                    //Find all toolbar and create active data provider
                    $activeDataToolbars = new CActiveDataProvider(
                        $toolbar,
                        [
                            'criteria' => $criteria,
                            'pagination' => [
                                'pageSize' => $this->getModule()->pagerSize,
                                'currentPage' => $page,
                            ],
                        ]
                    );
                    $this->renderPartial(
                        '_index',
                        [
                            'toolbar' => $toolbar,
                            'activeDataToolbars' => $activeDataToolbars
                        ]
                    );
                    Yii::app()->end();

                }
            }

            $this->redirect(['toolbar/index', 'page' => $page]);

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method save item in Toolbar ActiveRecord
     *
     * @param Toolbar $toolbar   Current Toolbar
     * @param string  $indexItem Index of item
     *
     * @throws Exception
     * @return boolean
     * @since  1.4
     */
    protected function saveItem($toolbar, $indexItem = null)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');

            //Get toolbar Core
            $dataJsonDecode = CJSON::decode($toolbar->toolbarCore);
            $toolbarItems = ($dataJsonDecode !== null) ? $dataJsonDecode : [];

            //Init date update
            $toolbar->toolbarDateUpdate = date('Y-m-d H:i:s');
            //If this index exists in items, get data
            $toolbar->scenario = 'createItem';
            $success = $toolbar->validate();
            if ($success === true) {
                $item = [
                    'title' => $toolbar->toolbarHtml,
                    'url' =>  $toolbar->toolbarUrl,
                    'type' => $toolbar->toolbarUrlType,
                    'options' => $toolbar->toolbarHtmlOptions,
                    'ItemOptions' => $toolbar->toolbarItemFonctOptions,
                    'items' => null,
                ];
                //Put new item in toolbar
                if (($indexItem !== null) && ($indexItem !== '')) {
                    $success = $this->manageItem(
                        $toolbarItems,
                        $item,
                        preg_split('/(_)/', $indexItem),
                        $toolbar->toolbarNewItemAction
                    );
                } else {
                    $toolbarItems[] = $item;
                }
            }
            //prepare JSON data
            if (($success === true) && (empty($toolbarItems) === false)) {
                //Re index array
                $indexedToolbar = $this->reIndexArray($toolbarItems);

                $toolbar->toolbarCore = CJSON::encode($indexedToolbar);
                $toolbar->save(false);
            }

            return $success;

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method delete item from nav toolbar
     *
     * @param $toolbarId Id of toolbar
     * @param $itemIndex Index in core toolbar where is item to delete
     * @throws Exception
     *
     * @return void
     * @since  1.2
     */
    public function actionDeleteItem($toolbarId, $indexItem)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');

            //Find toolbar
            $toolbar = Toolbar::model()->findByPk($toolbarId);
            $toolbarItems = [];
            $success = false;
            $htmlTargetIds = [
                'target' => '#nav',
                'untarget' => '#view-delete',
            ];

            //Get core data
            if ($toolbar !== null) {
                $dataJsonDecode = CJSON::decode($toolbar->toolbarCore);
                $toolbarItems = ($dataJsonDecode !== null) ? $dataJsonDecode : [];
            }

            $post = Yii::app()->getRequest()->getPost('response');

            if ($post !== null) {
                if ($post === 'yes') {
                    //Delete item
                    $success = $this->manageItem(
                        $toolbarItems,
                        null,
                        preg_split('/(_)/', $indexItem),
                        Toolbar::DELETE_TOOLBAR
                    );
                    //Save in data base
                    if ($success === true) {
                        $toolbar->toolbarCore = CJSON::encode($toolbarItems);
                        $toolbar->saveAttributes(['toolbarCore']);
                    }
                } else {
                    $success = null;
                }
            }

            if ($success === true) {
                //Re build toolbar
                $toolbarBuilding = $this->build($toolbarItems, $toolbarId, null, null, true);
                $textBuilder = $this->buildTextDescription($toolbarItems);
                $toolbar->toolbarTextDescription = CJSON::encode($textBuilder);
                $toolbar->save(false);
                $toolbar->toolbarTmpDescription = $textBuilder;

                //Render toolbar
                $this->renderPartial(
                    '_toolbar',
                    [
                        'toolbarBuilding' => $toolbarBuilding,
                        'toolbar' => $toolbar,
                        'indexItem' => null
                    ]
                );
            } elseIf ($success === false) {
                $this->renderPartial(
                    '_validate',
                    [
                        'htmlTargetIds' => $htmlTargetIds
                    ]
                );
            } else {
                $js = '
                    jQuery("#view-delete").parent().hide(
                        1,
                        function(){jQuery(this).children().html("");
                    });
                    ';
                $this->renderJs($js, true);
            }

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method find recursively element in array
     *
     * @param array  $toolbar     Array of core toolbar
     * @param array  $sourceItem  Source item to put
     * @param array  $targetIndex Wire of index to find item
     * @param string $action      Action to do
     *
     * @throws Exception
     * @return array|boolean
     * @since  1.2
     */
    protected function manageItem(&$toolbar, $sourceItem, $targetIndex, $action = 'get')
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            $toolbarItem = null;
            //For all index in wire
            for($i = 0; $i < count($targetIndex); $i++) {
                //Test if toolbar item exist with this index
                if ((isset($toolbar[intval($targetIndex[$i])]) === true) && (array_key_exists('items', $toolbar[intval($targetIndex[$i])]) === true)) {
                    //Test if it's last index and re call function
                    if (count($targetIndex) > 1) {
                        $toolbarItem = $this->manageItem(
                            $toolbar[intval($targetIndex[$i])]['items'],
                            $sourceItem,
                            array_slice($targetIndex, $i + 1, count($targetIndex) - 1),
                            $action
                        );
                    } else {
                        //Manage item with action type
                        //Delete
                        if ($action === Toolbar::DELETE_TOOLBAR) {
                            unset($toolbar[intval($targetIndex[$i])]);
                            $toolbarItem = true;
                            //Add item
                        } elseif ($action === Toolbar::GET_TOOLBAR) {
                            $toolbarItem = $toolbar[intval($targetIndex[$i])];
                            //Put new item
                        } elseif (($action === Toolbar::PUT_TOOLBAR) && ($sourceItem !== null)) {
                            if ((isset($toolbar[intval($targetIndex[$i])])) && (array_key_exists('items', $toolbar[intval($targetIndex[$i])]) === true)) {
                                $toolbar[intval($targetIndex[$i])]['items'][] = $sourceItem;
                                $toolbarItem = true;
                            }
                            //Update old item
                        } elseif (($action === Toolbar::UPDATE_TOOLBAR) && ($sourceItem !== null)) {
                            if ((isset($toolbar[intval($targetIndex[$i])])) && (array_key_exists('items', $toolbar[intval($targetIndex[$i])]) === true)) {
                                $toolbar[intval($targetIndex[$i])]['title'] = $sourceItem['title'];
                                $toolbar[intval($targetIndex[$i])]['url'] = $sourceItem['url'];
                                $toolbar[intval($targetIndex[$i])]['type'] = $sourceItem['type'];
                                $toolbar[intval($targetIndex[$i])]['options'] = $sourceItem['options'];
                                $toolbar[intval($targetIndex[$i])]['ItemOptions'] = $sourceItem['ItemOptions'];
                                $toolbarItem = true;
                            }
                        }
                    }
                } elseif((isset($toolbar[intval($targetIndex[$i])]) === false) && ($action === Toolbar::PUT_TOOLBAR)) {
                    if (count($targetIndex) > 1) {
                        $toolbarItem = $this->manageItem(
                            $toolbar[intval($targetIndex[$i])]['items'],
                            $sourceItem,
                            array_slice($targetIndex, $i + 1, count($targetIndex) - 1),
                            $action
                        );
                    } else {
                        $toolbar[$targetIndex[$i]] = $sourceItem;
                    }
                }
                break;
            }
            //Return item for add
            return $toolbarItem;

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method re index array
     *
     * @param array $toolbar Array to re indexed
     *
     * @throws Exception
     *
     * @return array
     * @since  1.2
     */
    protected function reIndexArray($toolbar)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            $newToolbar = null;
            if (is_array($toolbar) === true) {
                foreach ($toolbar as $item) {
                    $newToolbar[] = [
                        'title' => (isset($item['title']) === true) ? $item['title'] : null,
                        'url' => (isset($item['url']) === true) ? $item['url'] : null,
                        'type' => (isset($item['type']) === true) ? $item['type'] : null,
                        'options' => (isset($item['options']) === true) ? $item['options'] : null,
                        'ItemOptions' => (isset($item['ItemOptions']) === true) ? $item['ItemOptions'] : null,
                        'items' => (is_array($item['items']) === true) ? $this->reIndexArray($item['items']) : null,
                    ];
                }
            }
            return $newToolbar;
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method build toolbar
     *
     * @param array   $items        List of toolbar items
     * @param integer $toolbarId    Id of toolbar
     * @param string  $currentIndex Current index to check active
     * @param string  $index        Current index
     * @param boolean $first        First ul
     *
     * @throws Exception
     * @return string
     * @since  1.2
     */
    protected function build($items = [], $toolbarId = null, $currentIndex = null, $index = null, $first = false)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            if ($first === true) {
                $stringUl = '<ul class="sortableTree">';
            } else {
                $stringUl = '<ul>';

            }
            foreach($items as $i => $item) {
                //Generate indexation
                $indexation = ($index !== null) ? $index.'_'.$i:$i;
                //Add li
                $stringUl .=  Html::openTag(
                    'li',
                    [
                        'data-url-move' =>    Html::normalizeUrl(['toolbar/move', 'toolbarId' => $toolbarId]),
                        'data-mode' => 'replace',
                        'data-indexId' => $indexation,
                        'data-target' => '#nav',
                    ]
                );

                //Add Toolbar index
                $title = (empty($item['title']) === false) ?strip_tags( $item['title']) : Yii::t('default-toolbar', 'New item');
                $class = [];
                $class[] = 'btn-newItem';
                if (($currentIndex !== null) && ($currentIndex === $indexation)) {
                    $class[] = 'active';
                }
                //Add ajaxed link
                $stringUl .= '<span>'.Html::link(
                        $title,
                        [
                            'toolbar/load',
                            'toolbarId' => $toolbarId,
                            'indexItem' => $indexation,
                        ],
                        [
                            'class' => implode(' ', $class),
                            'data-url' => Html::normalizeUrl(
                                [
                                    'toolbar/load',
                                    'toolbarId' => $toolbarId,
                                    'indexItem' => $indexation,
                                ]
                            ),
                            'data-targetUrl' => Html::normalizeUrl(
                                [
                                    'toolbar/update',
                                    'toolbarId' => $toolbarId,
                                    'indexItem' => $indexation,
                                ]
                            ),
                            'data-refreshUrl' => Html::normalizeUrl(
                                [
                                    'toolbar/refresh',
                                    'toolbarId' => $toolbarId,
                                    'currentIndex' => $indexation,
                                ]
                            ),
                            'data-target' => '#itemForm',
                            'data-untarget' => '#itemForm',
                        ]
                    ).'</span>';
                //Ajaxed link delete
                $stringUl .= Html::link(
                    '<i class="icon-trash"></i>',
                    '#',
                    [
                        'class' => 'action btn-manage',
                        'data-target' => '#view-delete',
                        'data-untarget' => '#nav',
                        'data-url' => Html::normalizeUrl(
                            [
                                'toolbar/deleteItem',
                                'toolbarId' => $toolbarId,
                                'indexItem' => $indexation,
                            ]
                        ),
                    ]
                );
                if ((is_array($item) === true) && (array_key_exists('items', $item) === true) && ($item['items'] !== null) ) {
                    $stringUl .= $this->build($item['items'],$toolbarId,$currentIndex, $indexation);
                }
                $stringUl .= Html::closeTag('li');
            }
            $stringUl .= '</ul>';

            //Return
            return $stringUl;
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This method generate toolbar with text description
     *
     * @param Array   $tool
     * @param Toolbar $toolbar
     *
     * @throws Exception
     * @return boolean
     * @since  XXX
     */
    protected function generate(&$tool = [], &$toolbar)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            $lines = preg_split('/\r\n/',$toolbar->toolbarTmpDescription);
            $success = true;
            $matches = [];
            $target = null;
            $urlType = null;
            $oldTarget = '*';
            $levelIndex = 0;
            $title = null;
            $url = null;
            $oldKey = [];
            foreach($lines as $index => $line) {
                $line = strip_tags($line);
                $linesPieces = preg_split('/\s->\s/', $line);
                $options = null;
                if (count($linesPieces) <= 1) {
                    $success = false;
                    $toolbar->addError('toolbarTmpDescription', Yii::t('toolbar', 'Text description line: '.($index + 1).' in error'));
                    break;
                }
                //Get target adn title in linesPieces
                if(preg_match('/^(\*+)\s/', $linesPieces[0], $matches) === 1) {
                    $target = $matches[1];
                    $title = trim(preg_replace('/^(\*+)\s/', '', $linesPieces[0]));
                } else {
                    $success = false;
                    $toolbar->addError('toolbarTmpDescription', Yii::t('toolbar', 'Text description target in line: '.($index + 1).' in error'));
                    break;
                }
                //Get Element and url
                $urlAndOptions = preg_split('/\s=\s/', $linesPieces[1]);

                if (preg_match('/^([nctg]+)(\d+)/', $urlAndOptions[0], $matches) === 1) {
                    $urlType = 'sweelix';
                } elseif (preg_match('/^((http:|https:)\/\/[\w.\/]+)/', $urlAndOptions[0], $matches) === 1) {
                    $urlType = 'externe';
                    $url = $matches[1];
                } elseif (preg_match('/^(\w*( ?(\/?)\w*|\s))/', $urlAndOptions[0], $matches) === 1){
                    $urlType = 'interne';
                    $url = CJSON::encode([$matches[1]]);
                }
                if ($urlType === 'sweelix') {
                    $element = (isset(Toolbar::$listElement[strtolower($matches[1])][1]) === true) ? Toolbar::$listElement[strtolower($matches[1])][1] : null;
                    if ($element === null) {
                        $success = false;
                        $toolbar->addError('toolbarTmpDescription', Yii::t('toolbar', 'Element expected "n, c, t, g" element wrong in line : '.($index + 1)));
                        break;
                    }
                    $elementId = intval($matches[2]);
                    $attributeId = strtolower($element) . 'Id';
                    $criteria = new CriteriaBuilder(strtolower($element));
                    $criteria->filterBy($attributeId, $elementId);
                    $criteria->published();
                    $classElement = Toolbar::$listElement[strtolower($matches[1])][0];
                    $cmsElement = $classElement::model()->find($criteria->getCriteria());
                    if ($cmsElement === null) {
                        $success = false;
                        $toolbar->addError('toolbarTmpDescription', Yii::t('toolbar', 'Element not found please check cms element in line : '.($index + 1)));
                        break;
                    }
                    $url = CJSON::encode([$cmsElement->getRoute()]);
                } elseif ($urlType === 'interne') {
                    $urlController = preg_split('/\//', $url);
                    if (isset($urlController[0]) === true) {
                        $class = Search::getController($urlController[0]);
                        if ($class === '') {
                            $success = false;
                            $toolbar->addError('toolbarTmpDescription', Yii::t('toolbar', 'Element url error please check controller in line : '.($index + 1)));
                            break;
                        }
                    }
                }
                //Get options
                if (isset($urlAndOptions[1]) === true) {
                    //( ?(\{)(( ?(\"\w+\")\:\"[\w+\s]*\")\}))
                    if (preg_match('/^( ?(\{)((( ?(\w+)\:{1}\"[\w+\s]*\")( ?(\,{1})( ?(\w+)\:{1}\"[\w+\s]*\")|)+)\})|\s*)/', $urlAndOptions[1], $matches) === 1) {
                        $options = $urlAndOptions[1];
                    } else {
                        $success = false;
                        $toolbar->addError('toolbarTmpDescription', Yii::t('toolbar', 'Element options error please check options in line : '.($index + 1)));
                        break;
                    }
                }
                //Init item
                $item = [
                    'title' => $title,
                    'url' =>  $url,
                    'type' => $urlType,
                    'options' => $options,
                    'ItemOptions' => null,
                    'items' => [],
                ];
                if ((strlen($target) -1) === strlen($oldTarget)) { //Target > oldTarget
                    $levelIndex++;
                    $oldKey[] = 0;
                } elseif(strlen($target) < strlen($oldTarget)) { //Target < oldTarget
                    $diff = strlen($oldTarget) - strlen($target);
                    $oldKey = array_slice($oldKey, 0,-$diff);
                    $levelIndex -= $diff;
                    $oldKey[$levelIndex] ++;
                } elseif(strlen($target) === strlen($oldTarget)) { //Target === oldTarget
                    if (empty($oldKey) === true) {
                        $oldKey[0] = 0;
                    } else {
                        $oldKey[$levelIndex]++;
                    }
                } else {
                    //Level error
                    $success = false;
                    $toolbar->addError('toolbarTextDescription', Yii::t('toolbar', 'Arborescence wrong in line : '.($index + 1)));
                    break;
                }
                //Put new item
                $this->manageItem($tool, $item, $oldKey, 'put');
                $oldTarget = $target;
            }
            return $success;

        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }

    /**
     * This function recompute text description with array items
     *
     * @param array $items
     * @param int   $level
     *
     * @return string
     * @throws Exception
     * @since  XXX
     */
    protected function buildTextDescription($items, $level = 1)
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            $result = '';
            $matches = [];
            foreach($items as $item) {
                //Add level
                for($i = 0; $i < $level; $i++) {
                    $result .= '*';
                }
                //extract url
                $url = str_replace(['[', ']', '"'], '', $item['url']);
                $textUrl = '';
                $options = '';
                $isCms = false;
                $matchUrl = '';
                if (preg_match('/^((http:|https:)\/\/[\w.\/]+)/', $url, $matches) === 1) {
                    $textUrl = $matches[1];
                } elseif(preg_match('/^(\w*( ?(\/?)\w*|\s))/', $url, $matches) === 1) {
                    $matchUrl = $matches[1];
                    $isCms = true;
                }
                $result .= ' '.$item['title'].' -> ';
                if ($isCms === true) {
                    $routeDecode = RouteEncoder::decode($matchUrl);
                    if (empty($routeDecode) === false) {
                        foreach($routeDecode as $elementId => $value) {
                            if ($value !== null) {
                                switch($elementId) {
                                    case 0:
                                        $textUrl = 'c'.$value;
                                        break;
                                    case 1:
                                        $textUrl = 'n'.$value;
                                        break;
                                    case 2:
                                        $textUrl = 't'.$value;
                                        break;
                                    case 3:
                                        $textUrl = 'g'.$value;
                                        break;
                                }
                                break;
                            }
                        }
                    } else {
                        $textUrl = $matchUrl;
                    }
                }
                $result .= $textUrl;
                if (($item['options'] !== null) && (empty($item['options']) === false)) {
                    $result .= ' = '.$item['options'];
                }
                $result .= "\n";
                if ((is_array($item['items']) === true) && (empty($item['items']) === false)) {
                    $result .= $this->buildTextDescription($item['items'], $level + 1);
                }
            }
            return $result;
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\toolbar');
            throw $e;
        }
    }
    /**
     * Define filtering rules
     *
     * @return array
     * @since  1.2
     */
    public function filters() {
        return ['accessControl'];
    }

    /**
     * Define access rules / rbac stuff
     *
     * @return array
     * @since  1.2
     */
    public function accessRules() {
        return [
            [
                'allow', 'roles' => [$this->getModule()->getName()]
            ],
            [
                'deny', 'users'=> ['*'],
            ],
        ];
    }
}