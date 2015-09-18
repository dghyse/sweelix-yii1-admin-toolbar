<?php
/**
 *  _index.php
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
<div id="listToolbar">

    <table cellspacing="0">
        <thead>
        <tr>
            <th><?php echo Yii::t('toobar-index', 'name');?></th>
            <th><?php echo Yii::t('toobar-index', 'Status');?></th>
            <th class="last"><?php echo Yii::t('toobar-index', 'Actions');?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($activeDataToolbars->getData(true) as $i => $item) :?>
            <?php
            $class = [];
            if($item->toolbarIsActive == 1) {
                $class[] = 'active';
            }
            ?>
            <?php echo Html::openTag('tr', ['class' => implode(' ', $class)]); ?>
            <!-- CHECKBOX -->
            <?php echo Html::openTag('td',['class' => '']); ?>
            <?php
            echo $item->toolbarName;
            ?>
            <?php echo Html::closeTag('td');?>
            <td class="center">
                <?php echo ($item->toolbarIsActive == 0) ? Yii::t('index-toolbar', 'Inactive') :Yii::t('index-toolbar', 'Active');?>
            </td>

            <td class="actions">
                <?php
                    echo Html::link(
                        '<i class="icon-search"></i>'.Yii::t('index-toolbar', 'update'),
                        Html::normalizeUrl(
                            [
                                'toolbar/update',
                                'toolbarId' => $item->toolbarId,
                            ]
                        ),
                        [
                            'class' => 'button'
                        ]
                    );
                ?>

                <?php

                    $url = Html::normalizeUrl(
                        [
                            'toolbar/activate',
                            'toolbarId' => $item->toolbarId,
                            'action' => 'activate',
                            'page' => $activeDataToolbars->getPagination()->getCurrentPage(),
                        ]
                    );
                    $options = [
                        'class' => 'button medium btn-manage',
                        'data-target' => '#listToolbar',
                        'data-url' => $url,
                    ];

                    $title = Yii::t('index-toolbar', 'Active');
                    if ($item->toolbarIsActive == 1) {
                        $url = Html::normalizeUrl(
                            [
                                'toolbar/activate',
                                'toolbarId' => $item->toolbarId,
                                'action' => 'desactivate',
                                'page' => $activeDataToolbars->getPagination()->getCurrentPage(),
                            ]
                        );
                        $options['data-url'] = $url;
                        $title = Yii::t('index-toolbar', 'Déactiver');

                    }

                    echo Html::link(
                        '<i class="icon-edit"></i>'.$title,
                        $url,
                        $options
                    );
                ?>
                <?php
                    echo Html::link(
                        '<i class="icon-trash"></i>'.Yii::t('index-toolbar', 'Delete'),
                        Html::normalizeUrl(
                            [
                                'toolbar/delete',
                                'toolbarId' => $item->toolbarId,
                                'page' => $activeDataToolbars->getPagination()->getCurrentPage(),
                            ]
                        ),
                        [
                            'class' => 'button danger btn-manage',
                            'data-url' => Html::normalizeUrl(
                                [
                                    'toolbar/delete',
                                    'toolbarId' => $item->toolbarId,
                                    'page' => $activeDataToolbars->getPagination()->getCurrentPage(),
                                ]
                            ),
                            'data-untarget' => '#listToolbar',
                            'data-target' => '#view-delete'
                        ]
                    );
                ?>
            </td>
            <?php echo Html::closeTag('tr');?>
        <?php endforeach;?>
        </tbody>
    </table>
    <?php
    // define pagination offset
    $start = max($activeDataToolbars->getPagination()->getCurrentPage() - 1, 1);
    $end = min($activeDataToolbars->getPagination()->getCurrentPage() + 3, $activeDataToolbars->getPagination()->getPageCount());
    ?>
    <div class="pagination">
        <?php  if ($activeDataToolbars->getPagination()->getCurrentPage() > 0) :?>
            <?php
                echo Html::link(
                    '<i class="icon-arrow-left"></i>',
                    Html::normalizeUrl(
                        [
                            'toolbar/index',
                            'page' => $activeDataToolbars->getPagination()->getCurrentPage() - 1,
                        ]
                    ),
                    [
                        'class' => 'button small btn-manage first',
                        'data-target' => '#listToolbar',
                        'data-url' => Html::normalizeUrl(
                            [
                                'toolbar/index',
                                'page' => $activeDataToolbars->getPagination()->getCurrentPage() - 1,
                            ]
                        ),
                    ]
                );
            ?>
        <?php endif; ?>

        <?php if(($start-$end) !== 0):?>
            <?php for ($i = $start ; $i <= $end ; $i++) : ?>
                <?php
                    $htmlOptions = [
                        'class' => 'button btn-manage small',
                        'data-target' => '#listToolbar',
                        'data-url' => Html::normalizeUrl(
                            [
                                'toolbar/index',
                                'page' => $i-1,
                            ]
                        ),
                    ];
                    if(($i - 1) === $activeDataToolbars->getPagination()->getCurrentPage()) {
                        $htmlOptions['class'] .= ' info';
                    }
                ?>
                <?php
                    echo Html::link(
                        $i,
                        Html::normalizeUrl(
                            [
                                'toolbar/index',
                                'page' => $i-1,
                            ]
                        ),
                        $htmlOptions
                    );
                ?>
            <?php endfor; ?>
        <?php endif;?>
        <?php if ($activeDataToolbars->getPagination()->getCurrentPage() < ($activeDataToolbars->getPagination()->getPageCount() -1 )): ?>
            <?php
                echo Html::link(
                    '<i class="icon-arrow-right"></i>',
                    Html::normalizeUrl(
                        [
                            'toolbar/index',
                            'page' => $activeDataToolbars->getPagination()->getCurrentPage() + 1,
                        ]
                    ),
                    [
                        'class' => 'button small btn-manage last',
                        'data-target' => '#listToolbar',
                        'data-url' => Html::normalizeUrl(
                            [
                                'toolbar/index',
                                'page' => $activeDataToolbars->getPagination()->getCurrentPage() + 1,
                            ]
                        ),
                    ]
                );
            ?>
        <?php endif;  ?>
    </div>
</div>