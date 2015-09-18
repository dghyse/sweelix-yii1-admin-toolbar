<?php
/**
 *  create.php
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
<nav class="toolbar">
    <?php
    $this->renderPartial(
        '_toolbar',
        [
            'toolbarBuilding'=> $toolbarBuilding,
            'toolbar' => $toolbar,
            'indexItem' => $indexItem
        ]
    );
    ?>
</nav>
<section>
    <fieldset class="container hidden">
        <div  id="itemForm">
        </div>
    </fieldset>
    <fieldset class="container hidden">
        <div  id="searchElement">
        </div>
    </fieldset>
    <fieldset class="container hidden">
        <div id="view-delete">
        </div>
    </fieldset>
</section>

