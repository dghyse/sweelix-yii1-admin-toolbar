<?php
/**
 * Search.php
 *
 * PHP version 5.4+
 *
 * @author      David Ghyse <dghyse@ibitux.com>
 * @link        http://code.ibitux.net/projects/isweelix
 * @copyright   Copyright 2010-2015 Ibitux
 * @license     http://www.ibitux.com/license license
 * @version     1.4
 * @category    models
 * @package     application\models
 */

namespace sweelix\yii1\admin\toolbar\models;

use sweelix\yii1\ext\db\CriteriaBuilder;
use CFormModel;
use CJSON;
use ReflectionClass;
use Yii;

/**
 * This is the model Search.
 *
 * The followings are the available columns in table 'toolbars':
 */
class Search extends CFormModel
{
    /**
     * Const Type element to search
     *
     * @var string
     */
    const SEARCH_SWEELIX = 'sweelix';

    /**
     * Const Type element to search
     *
     * @var string
     */
    const SEARCH_INTERNE = 'interne';


    /**
     * name of element
     *
     * @var string $searchElement
     */
    public $searchElement = null;

    /**
     * Url
     *
     * @var string $searchUrl
     */
    public $searchUrl;

    /**
     * Params of url
     *
     * @var string $searchUrlParams
     */
    public $searchUrlParams = null;

    /**
     * Tmp Url
     *
     * @var string $searchUrlTmp
     */
    public $searchUrlTmp;

    /**
     * Json url
     *
     * @var string $searchJsonUrl
     */
    public $searchJsonUrl;

    /**
     * Element type ('sweelix', 'interne', 'externe')
     *
     * @var string $searchElementType
     */
    public $searchElementType;

    /**
     * Search element list
     *
     * @var array $searchListElements
     */
    public  $searchListElements = [];

    /**
     *
     *
     * @var array $searchPropertiesElement
     */
    public $searchPropertiesElement = [];

    /**
     * This method construct new instance
     *
     * @param string $scenario    Current scenario
     * @param string $elementType Type of element to search
     *
     * @throws Exception
     *
     * @return Search
     * @since  1.2
     */
    public function __construct($scenario = '', $elementType = 'sweelix')
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\models\toolbar');

            $this->searchElementType = $elementType;
            if ($elementType === self::SEARCH_SWEELIX) {
                $this->searchListElements = [
                    'node' => 'node',
                    'content' => 'content',
                    'tag' => 'tag',
                    'group' => 'group',
                ];
            } elseif ($elementType === self::SEARCH_INTERNE) {
                $controllersPath = Yii::app()->controllerPath;
                $controllers = scandir($controllersPath);
                foreach($controllers as $controller) {
                    $extension = pathinfo($controller, PATHINFO_EXTENSION);
                    switch($extension) {
                        case 'php':
                            $class = basename($controller, '.php');
                            $this->searchListElements[$class] = self::getController($class);
                            break;
                        default:
                            break;
                    }
                }
            }
            parent::__construct($scenario);
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\models\toolbar');
            throw $e;
        }
    }


    /**
     * Rules
     *
     * @return array validation rules for model attributes.
     * @since  1.2
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['searchElement, searchUrlParams, searchUrl, searchUrlTmp, searchJsonUrl', 'safe'],
            ['searchElement', 'required', 'on' => 'searchElement'],
            ['searchUrl', 'required', 'on' => ['findElement', 'addUrl']],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array customized attribute labels (name=>label)
     * @since  1.2
     */
    public function attributeLabels()
    {
        return array(
            'searchType' => Yii::t('search-model', 'search type'),
        );
    }

    /**
     * This method search element Type in Sweelix or application
     *
     * @return array
     * @since  1.2
     */
    public function searchElements()
    {
        $urls = [];
        if ($this->searchElementType === self::SEARCH_SWEELIX) {
            $element = $this->searchElement;
            $elementTitle = $element . 'Title';
            $elementOrder = null;

            if ($element === 'node') {
                $elementOrder = $element . 'LeftId';
            } elseif ($element === 'content') {
                $elementOrder = $element . 'Order';
            } else {
                $elementOrder = $elementTitle;
            }

            $criteriaBuilder = new CriteriaBuilder($this->searchElement);
            $criteriaBuilder->published();
            if ($elementOrder !== null) {
                $criteriaBuilder->orderBy($elementOrder);
            }

            $modelClass = '\\sweelix\\yii1\\ext\\entities\\' . ucfirst($element);
            $elements = $modelClass::model()->findAll($criteriaBuilder->getCriteria());

            foreach ($elements as $key => $value) {
                $urls[CJSON::encode([$value->getRoute()])] = $value->$elementTitle;
            }
        } elseif ($this->searchElementType === self::SEARCH_INTERNE) {
            $element = $this->searchElement;

            //include class
            $className = self::getController($element);
            if($className !== '') {

                $controllersNS = Yii::app()->controllerNamespace;

                if ($controllersNS !== '') {
                    $className = $controllersNS.'\\'.$className;
                }
                //Reflection
                $reflection = new ReflectionClass($className);

                //Get methods and parse it
                $methods = $reflection->getMethods();
                foreach($methods as $method) {
                    $methodName = $method->name;
                    $controller = lcfirst(preg_replace('/(Controller)/', '', $element));
                    $matches = [];
                    if (preg_match('/(action)[\w]{2,}/', $methodName, $matches) === 1) {
                        $action = lcfirst(preg_replace('/(action)/', '', $methodName));
                        $url = $controller . '/' . $action;

                        $urls[CJSON::encode([$url])] = $action;
                    }
                }
            }
        }
        return $urls;
    }

    /**
     * This method check if class controller exists
     *
     * @param $controller Name of controller
     *
     * @return string
     * @since  1.4
     */
    public static function getController($controller)
    {
        //Init params
        $class = '';
        $controllersPath = Yii::app()->controllerPath;
        $controllersNS = Yii::app()->controllerNamespace;
        $classFile=$controllersPath.DIRECTORY_SEPARATOR.$controller.'.php';
        $classRepertory = substr($controllersPath, strrpos($controllersPath, DIRECTORY_SEPARATOR,-1) + 1);
        $appControllersAlias = 'application.'.$classRepertory;

        $className = $appControllersAlias.'.'.$controller;
        if ($controllersNS !== null) {
            $className = $controllersNS.'\\'.$controller;
        }

        if(is_file($classFile) === true) {
            $classNameImported = Yii::import($className, true);
            if((class_exists($classNameImported,false) ===true) && (is_subclass_of($classNameImported,'CController') ===true)) {
                $class = $controller;
            }
        }
        return $class;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name.
     *
     * @return Toolbar the static model class
     * @since  1.2
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
