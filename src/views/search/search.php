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

?>
<section>
    <fieldset>
        <?php
        $this->renderPartial(
            '_search',
            [
                'elements' => $elements,
                'search' => $search,
                'toolbar' => $toolbar,
            ]
        );
        ?>
    </fieldset>
</section>