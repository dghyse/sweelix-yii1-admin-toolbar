<?php
/**
 *  index.php
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
<section>
    <fieldset class="hidden">
        <div id="view-delete"></div>
    </fieldset>
    <fieldset>
        <?php echo Html::beginForm();?>
        <div>
            <?php echo Html::activeLabel($toolbar, 'toolbarName', ['class' => 'medium']);?>
        </div>
        <div>
            <?php echo Html::activeTextField($toolbar, 'toolbarName', ['class' => 'textbox medium']);?>
        </div>
        <div>
            <?php echo Html::activeLabel($toolbar, 'toolbarTextDescription', ['class' => 'medium']);?>
        </div>
        <div id="textDescription">
            <?php echo Html::activeTextArea($toolbar, 'toolbarTmpDescription', ['class' => 'medium', 'rows' => 15, 'cols' => 50]);?>
        </div>
        <div>
            <?php
            echo Html::submitButton(
                Yii::t('toolbar-index', 'Create new'),
                [
                    'class' => 'button',
                ]
            );
            ?>
        </div>
        <?php if($toolbar->hasErrors() === true): ?>
            <?php if(isset($toolbar->errors['toolbarTmpDescription']) === true):?>
                <span class="error">
                <?php
                echo $toolbar->errors['toolbarTmpDescription'][0];
                ?>
            </span>
            <?php endif;?>
        <?php endif;?>
        <?php $this->renderPartial('_index', ['toolbar' => $toolbar, 'activeDataToolbars' => $activeDataToolbars]); ?>
        <?php echo Html::endForm();?>
    </fieldset>
</section>
