<?php
/**
 * Html.php
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

use sweelix\yii1\web\helpers\Html as BaseHtml;

/**
 *  Coomposent Html
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
class Html extends BaseHtml
{

    /**
     * This method rendering input
     *
     * @param $type
     * @param $model
     * @param $attribute
     * @param array $options
     *
     * @return mixed
     * @since  1.0
     */
    public static function activeInputField($type, $model, $attribute, $options = [])
    {
        if ($model->hasErrors($attribute) === true) {
            $options['class'] = isset($options['class'])?($options['class'].' error'):'error';
        }
        return parent::activeInputField($type, $model, $attribute, $options);
    }
}
