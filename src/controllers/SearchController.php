<?php
/**
 *  SearchController.php
 *
 *  PHP version 5.4+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/licence licence
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  controllers
 * @package   sweelix\yii1\admin\toolbar\controllers
 */

namespace sweelix\yii1\admin\toolbar\controllers;

use sweelix\yii1\admin\toolbar\components\Html;
use sweelix\yii1\admin\toolbar\models\Search;
use sweelix\yii1\admin\toolbar\models\Toolbar;
use sweelix\yii1\admin\core\web\Controller;
use CJSON;
use CLogger;
use Exception;
use Yii;

/**
 * this class is used to manage search
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/licence licence
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  controllers
 * @package   sweelix\yii1\admin\toolbar\controllers
 */

class SearchController extends Controller
{

    /**
     * This method render index
     *
     * @param string $elementType Type of element to find
     *
     * @throws Exception
     * @return void
     * @since  1.2
     */
    public function actionSearch($elementType = 'sweelix')
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\Search');
            $search = new Search('findElement', $elementType);
            $toolbar = new Toolbar();
            $elements = [];
            //Get post
            $post = Yii::app()->getrequest()->getPost(Html::modelName($search), null);

            //Get clicked button loading
            $loading = Yii::app()->getRequest()->getPost('loading', null);

            //Check post
            if ($post !== null) {
                $search ->attributes = $post;
                $search->searchUrl = $search->searchUrlTmp;
                $elements = $search->searchElements();

                if (empty($search->searchUrlParams) === false) {
                    $params = CJSON::decode($search->searchUrlParams);
                    $route = CJSON::decode($search->searchUrl);
                    foreach($params as $key => $value) {
                        $route[$key] = $value;
                    }
                   $search->searchJsonUrl = CJSON::encode($route);
                } else {
                    $search->searchJsonUrl = $search->searchUrl;
                }

                //If loading elements
                if ($loading === null) {
                    //if element is choose
                   $success = $search->validate();
                }
            }
            if (Yii::app()->getRequest()->isAjaxRequest === true) {
                $this->renderPartial(
                    '_search',
                    [
                        'elements' => $elements,
                        'search' => $search,
                        'toolbar' => $toolbar,
                    ]

                );

            } else {
                $this->render(
                    'search',
                    [
                        'elements' => $elements,
                        'search' => $search,
                        'toolbar' => $toolbar,
                    ]

                );
            }
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\Search');
            throw $e;
        }
    }

    /**
     * This method render get url
     *
     * @throws Exception
     *
     * @return void
     * @since  1.2
     */
    public function actionUrl()
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\controllers\Search');
            $search = new Search('addUrl');
            $search->searchElementType = 'externe';
            $toolbar = new Toolbar();
            //Get post
            $post = Yii::app()->getrequest()->getPost(Html::modelName($search), null);

            //Check post
            if ($post !== null) {
                $search ->attributes = $post;
                $search->validate();
            }
            //Render
            if (Yii::app()->getRequest()->isAjaxRequest === true) {
                $this->renderPartial(
                    '_url',
                    [
                        'search' => $search,
                        'toolbar' => $toolbar,
                    ]

                );

            } else {
                $this->render(
                    'url',
                    [
                        'search' => $search,
                        'toolbar' => $toolbar,
                    ]

                );
            }


        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\controllers\Search');
            throw $e;
        }
    }
}