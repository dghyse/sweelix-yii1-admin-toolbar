<?php
/**
 *  search.php
 *
 *  PHP version 5.3+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/licence licence
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  views
 * @package   sweelix.yii1.admin.toolbar.views.search
 *
 */

use sweelix\yii1\admin\toolbar\components\Html;
use sweelix\yii1\admin\toolbar\models\Search;
?>
<?php echo Html::beginForm(['search/search', 'elementType' => $search->searchElementType], 'post');?>

<div>
    <?php echo Html::activeDropDownList($search, 'searchElement', $search->searchListElements, ['class' => 'dropdown medium']);?>

    <?php
        echo Html::submitButton(
            Yii::t('toolbar-create', 'load'),
            [
                'class' => 'button btn-load',
                'type' => 'submit',
                'name' => 'loading',
                'data-url' => Html::normalizeUrl(['search/search', 'elementType' => $search->searchElementType]),
                'data-action' => 'loading',
            ]
        );
    ?>
</div>

<div>
    <?php
        echo Html::activeDropDownList(
            $search,
            'searchUrlTmp',
            $elements,
            [
                'class' => 'dropdown medium listElements',
                'data-targetName' => Html::activeName($search, 'searchUrl'),
            ]
        );
    ?>
    <?php echo Html::activeTextField($search, 'searchUrl', ['class' => 'textbox medium', 'disabled' => 'disabled']);?>
    <?php echo Html::activeHiddenField($search, 'searchJsonUrl');?>

    <?php if ($search->searchElementType !== Search::SEARCH_SWEELIX):?>
        <?php echo Html::activeTextField($search, 'searchUrlParams', ['class' => 'textbox medium', 'placeholder' => Yii::t('toolbar-search', 'url params')]);?>
    <?php endif;?>
</div>
<div>
    <?php
        echo Html::submitButton(
            Yii::t('search-index', 'Ok'),
            [
                'class' => 'button btn-save-url',
                'data-url' => Html::normalizeUrl(['search/search', 'elementType' => $search->searchElementType]),
                'data-source' => Html::activeName($search, 'searchJsonUrl'),
                'data-target' => Html::activeName($toolbar, 'toolbarUrl'),
                'data-inputType' => Html::activeName($toolbar, 'toolbarUrlType'),
                'data-elementType' => $search->searchElementType,
            ]
        );
    ?>
</div>
<?php echo Html::endForm();?>
