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
<?php
echo Html::openTag(
    'div',
    [
        'data-refreshUrl' => Html::normalizeUrl(
            [
                'toolbar/refresh',
                'toolbarId' => $toolbar->toolbarId,
                'currentIndex' => $indexItem
            ]
        ),
        'id' => 'nav',
    ]
);
?>
<fieldset>
    <?php echo Html::beginForm(['toolbar/update', 'toolbarId' => $toolbar->toolbarId, 'indexItem' => $indexItem], 'post');?>
    <div>
        <?php echo Html::activeTextField($toolbar, 'toolbarName', ['class' => 'textbox medium', 'disabled' => 'disabled']);?>
        <?php
        echo Html::submitButton(
            Yii::t('toolbar-create', 'add new item'),
            [
                'class' => 'button  btn-addItem',
                'type' => 'submit',
                'name' => 'Adding',
                'value' => 'Adding',
                'id' => 'createNewItem',
                'data-url' => Html::normalizeUrl(
                    [
                        'toolbar/update',
                        'toolbarId' => $toolbar->toolbarId,
                        'indexItem' => $indexItem,
                    ]
                ),
                'data-target' => '#itemForm',
                'data-untarget' => 'itemForm',
            ]
        );
        ?>

    </div>
    <div id="textDescription">
        <?php echo Html::activeTextArea($toolbar, 'toolbarTmpDescription', ['class' => 'medium', 'rows' => 15, 'cols' => 50]);?>
        <?php if($toolbar->hasErrors() === true): ?>
            <?php if(isset($toolbar->errors['toolbarTmpDescription']) === true):?>
                <span class="error">
                    <?php
                    echo $toolbar->errors['toolbarTmpDescription'][0];
                    ?>
                    </span>
            <?php endif;?>
        <?php endif;?>
        <?php
        echo Html::submitButton(
            Yii::t('toolbar-create', 'Recompute toolbar'),
            [
                'class' => 'button',
                'type' => 'submit',
                'name' => 'Recompute',
                'value' => 'Recompute',
                'id' => 'recomputeItem',
                'data-url' => Html::normalizeUrl(
                    [
                        'toolbar/update',
                        'toolbarId' => $toolbar->toolbarId,
                    ]
                ),
                'data-target' => '#itemForm',
                'data-untarget' => 'itemForm',
            ]
        );
        ?>
    </div>
    <?php echo Html::endForm();?>
</fieldset>

<div id="treemenu">
    <nav class="toolbar">
        <?php echo $toolbarBuilding; ?>
    </nav>
</div>
<?php echo Html::closeTag('div');?>
