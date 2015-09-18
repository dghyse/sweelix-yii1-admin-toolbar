<?php
/**
 *  _create.php
 *
 *  PHP version 5.3+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/licence licence
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  views
 * @package   sweelix.yii1.admin.toolbar.views.toolbar
 *
 */

use sweelix\yii1\admin\toolbar\components\Html;
?>
<?php echo Html::beginForm(['toolbar/update', 'toolbarId' => $toolbar->toolbarId, 'indexItem' => $indexItem], 'post');?>

<div>
    <?php echo Html::activeHiddenField($toolbar, 'toolbarUrlType');?>
    <?php echo Html::activeHiddenField($toolbar, 'toolbarNewItemAction');?>
</div>
<div>
    <?php echo Html::activeTextField($toolbar, 'toolbarHtml', ['class' => ' large', 'placeholder' => Yii::t('update-toolbar', 'Titre Html')]); ?>
</div>
<?php echo Html::activeTextField($toolbar, 'toolbarUrl', ['class' => 'large', 'readOnly' => 'readonly', 'placeholder' => Yii::t('update-toolbar', 'Url')]); ?>
<div>
    <?php echo Html::activeTextArea($toolbar, 'toolbarHtmlOptions', ['class' => ' large', 'readOnly', 'placeholder' => Yii::t('update-toolbar', 'Options Html')]); ?>
</div>
<div>
    <?php echo Html::activeTextArea($toolbar, 'toolbarItemFonctOptions', ['class' => ' large', 'readOnly', 'placeholder' => Yii::t('update-toolbar', 'Fonctionnals options')]); ?>
</div>
<div>
    <?php
    echo Html::link(
        Yii::t('toolbar-items', 'Elements'),
        ['search/search', 'elementType' => 'sweelix'],
        [
            'class' => 'button btn-addItem',
            'data-url' => Html::normalizeUrl(['search/search']),
            'data-target' => '#searchElement',
        ]
    );
    ?>
    <?php
    echo Html::link(
        Yii::t('toolbar-items', 'Controllers'),
        ['search/search', 'elementType' => 'interne'],
        [
            'class' => 'button medium btn-addItem',
            'data-url' => Html::normalizeUrl(['search/search', 'elementType' => 'interne']),
            'data-target' => '#searchElement',
        ]
    );
    ?>
    <?php
    echo Html::link(
        Yii::t('toolbar-items', 'Externe'),
        ['search/url'],
        [
            'class' => 'button btn-addItem',
            'data-url' => Html::normalizeUrl(['search/url']),
            'data-target' => '#searchElement',
        ]
    )
    ;
    ?>
</div>
<div>
    <?php
    echo Html::submitButton(
        Yii::t('toolbar-index', 'Save'),
        [
            'class' => 'button btn-action',
            'data-url' => Html::normalizeUrl(['toolbar/update', 'toolbarId' => $toolbar->toolbarId, 'indexItem' => $indexItem]),
        ]
    );
    ?>
</div>
<?php echo Html::endForm();?>
