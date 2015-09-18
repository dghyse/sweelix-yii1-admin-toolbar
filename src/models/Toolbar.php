<?php
/**
 * Toolbar.php
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
use CActiveRecord;
use Exception;
use Yii;

/**
 * This is the model class for table "toolbars".
 *
 * The followings are the available columns in table 'toolbars':
 * @property integer $toolbarId
 * @property string $toolbarName
 * @property string $toolbarCore
 * @property string $toolbarTextDescription
 * @property integer $toolbarIsActive
 * @property string $toolbarDateCreate
 * @property string $toolbarDateUpdate
 * @porperty string $toolbarUrl
 * @property string $toolbarHtml
 * @property strin $toolbarHtmlOptions
 */
class Toolbar extends CActiveRecord
{

	/**
	 * Const to get item
	 *
	 * @var string
	 */
	const GET_TOOLBAR = 'get';

	/**
	 * Const to delete item
	 *
	 * @var string
	 */
	const DELETE_TOOLBAR = 'delete';

	/**
	 * Cont to put item
	 *
	 * @var string
	 */
	const PUT_TOOLBAR = 'put';

	/**
	 * Const to update item
	 *
	 * @var string
	 */
	const UPDATE_TOOLBAR = 'update';

	/**
	 * Const target after to move
	 *
	 * @var string
	 */
	const TARGET_AFTER = 'after';

	/**
	 * Const target before to move
	 *
	 * @var string
	 */
	const TARGET_BEFORE = 'before';

	/**
	 * Const target in to move
	 *
	 * @var string
	 */
	const TARGET_IN = 'in';

	/**
	 * Url of new item
	 *
	 * @var string $toolbarUrl
	 */
	public $toolbarUrl = null;

	/**
	 * Html of title for new item
	 *
	 * @var string $toolbarHtml
	 */
	public $toolbarHtml = null;

	/**
	 * Options of url link item
	 *
	 * @var string $toolbarHtmlOptions
	 */
	public $toolbarHtmlOptions = null;

	/**
	 * Type of url (sweelix, interne, externe)
	 *
	 * @var string $toolbarUrlType
	 */
	public $toolbarUrlType = null;

	/**
	 * Title of element sweelix find
	 *
	 * @var string $toolbarElementTitle
	 */
	public $toolbarElementTitle = null;

	/**
	 * Id of element Sweelix
	 *
	 * @var string $toolbarElementId
	 */
	public $toolbarElementId = null;

	/**
	 * Type of new item action ('update', 'put')
	 *
	 * @var string $toolbarNewItemType
	 */
	public $toolbarNewItemAction;

	/**
	 * Fonctionals options
	 *
	 * @var $string $toolbarItemFonctOptions
	 */
	public $toolbarItemFonctOptions;

	/**
	 * @var string $toolbarTextDesciption
	 */
	public $toolbarTmpDescription = null;

	public static $listElement = [
		'n' =>  ['sweelix\yii1\ext\entities\Node', 'node'],
		'c' => ['sweelix\yii1\ext\entities\Content', 'content'],
		't' => ['sweelix\yii1\ext\entities\Tag', 'tag'],
		'g' => ['sweelix\yii1\ext\entities\Group', 'group'],
	];

	/**
	 * Table name
	 *
	 * @return string the associated database table name
	 * @since  1.2
	 */
	public function tableName()
	{
		return 'toolbars';
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
			['toolbarIsActive', 'numerical', 'integerOnly'=>true],
			['toolbarName', 'length', 'max'=>255],
			['toolbarCore, toolbarDateCreate, toolbarDateUpdate', 'safe'],
			['toolbarUrl, toolbarHtml, toolbarHtmlOptions, toolbarUrlType, toolbarNewItemAction, toolbarItemFonctOptions, toolbarTmpDescription', 'safe'],

			['toolbarName', 'required', 'on' => ['createName']],

			['toolbarName', 'unique','className' => 'sweelix\yii1\admin\toolbar\models\Toolbar', 'on' => ['create', 'update', 'createName']],

			['toolbarUrl, toolbarHtml', 'required', 'on' => 'update'],
			['toolbarUrl, toolbarHtml', 'required', 'on' => 'createItem'],


			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			['toolbarId, toolbarName, toolbarCore, toolbarIsActive, toolbarDateCreate, toolbarDateUpdate', 'safe', 'on'=>'search'],
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
		return [
			'toolbarId' => Yii::t('toolbar-model', 'Toolbar'),
			'toolbarName' => Yii::t('toolbar-model', 'Toolbar name'),
			'toolbarCore' => Yii::t('toolbar-model', 'Toolbar core'),
			'toolbarIsActive' => Yii::t('toolbar-model', 'Toolbar is active'),
			'toolbarDateCreate' => Yii::t('toolbar-model', 'Toolbar date create'),
			'toolbarDateUpdate' => Yii::t('toolbar-model', 'Toolbar date update'),
			'toolbarTextDescription' => Yii::t('toolbar-model', 'Text description'),
		];
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('toolbarId',$this->toolbarId);
		$criteria->compare('toolbarName',$this->toolbarName,true);
		$criteria->compare('toolbarCore',$this->toolbarCore,true);
		$criteria->compare('toolbarIsActive',$this->toolbarIsActive);
		$criteria->compare('toolbarDateCreate',$this->toolbarDateCreate,true);
		$criteria->compare('toolbarDateUpdate',$this->toolbarDateUpdate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
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
