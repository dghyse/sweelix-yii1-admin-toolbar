<?php
/**
 *  _validate.php
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

<?php echo Html::beginForm(); ?>
<p>
    <?php echo Yii::t('validate', 'Etes vous sûr de vouloir supprimer cet item');?>
</p>
<?php
echo Html::submitButton(
    Html::tag(
        'span',
        [],
        Yii::t(
            'validate',
            'Oui'
        )
    ),
    [
        'type'=>'submit',
        'name' => 'response',
        'value' => 'yes',
        'class'=> 'btn-manage',
        'data-url' => Html::normalizeUrl(
            []
        ),
        'data-target' => $htmlTargetIds['target'],
        'data-untarget' => $htmlTargetIds['untarget'],
    ]
);
?>
<?php
echo Html::submitButton(
    Html::tag(
        'span',
        [],
        Yii::t(
            'validate',
            'Non'
        )
    ),
    [
        'type'=>'submit',
        'name' => 'response',
        'value' => 'no',
        'class'=> 'btn-manage',
        'data-url' => Html::normalizeUrl(
            []
        ),
        'data-target' => $htmlTargetIds['untarget'],
        'data-untarget' => $htmlTargetIds['untarget'],
    ]
);
?>
<?php echo Html::endForm();?>
