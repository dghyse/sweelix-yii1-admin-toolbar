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
?>
<?php echo Html::beginForm();?>

<div>

    <?php echo Html::activeTextField($search, 'searchUrl', ['class' => 'textbox classic']);?>
</div>
<div>
    <?php
    echo Html::submitButton(
        Yii::t('search-index', 'Ok'),
        [
            'class' => 'button btn-load',
            'data-url' => Html::normalizeUrl(['search/url']),
            'data-source' => Html::activeName($search, 'searchUrl'),
            'data-target' => Html::activeName($toolbar, 'toolbarUrl'),
            'data-inputType' => Html::activeName($toolbar, 'toolbarUrlType'),
            'data-elementType' => $search->searchElementType,
        ]
    ); ?>
</div>
<?php echo Html::endForm();?>
