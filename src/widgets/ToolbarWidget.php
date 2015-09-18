<?php
/**
 * ToolbarWidget.php
 *
 * PHP version 5.4+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  widgets
 * @package   toolbar\widgets
 */

namespace sweelix\yii1\admin\toolbar\widgets;

use sweelix\yii1\admin\toolbar\components\Html;
use sweelix\yii1\admin\toolbar\components\Toolbar;
use Exception;
use CWidget;

/**
 * This class manage Toolbar
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  widgets
 * @package   toolbar.widgets
 *
 */

class ToolbarWidget extends CWidget
{

    /**
     * @var string
     */
    public $toolbarName;

    /**
     * @var string $containerHtml
     */
    public $htmlContainer = '<ul class="test">{toolbar}</ul>';

    /**
     * @var string $mainContainer
     */
    public $mainContainer = '<nav class="list-unstyled">{toolbar}</nav>';

    /**
     * @var string $containerTitleHtml
     */
    public $containerTitleHtml = '<span>{title}</span>';

    /**
     * This method init widget
     *
     * @throws \Exception
     *
     * @return void
     * @since  1.2
     */
    public function init() {
        try {
            \Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\widgets\Toolbar');
            parent::init();
        } catch(\Exception $e) {
            \Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\widgets\Toolbar');
            throw $e;
        }
    }

    /**
     * This method run widget
     * @throws \Exception
     *
     * @return void
     * @since  1.2
     */
    public function run() {
        try {
            \Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\widgets\Toolbar');

            $toolbar = new Toolbar($this->toolbarName);
            $items = $toolbar->getToolbar();
            //Render Toolbar
            $renderingToolbar = $this->buildToolbar($items);

            $renderingToolbar = str_replace('{toolbar}', $renderingToolbar, $this->mainContainer);
            $this->render(
                'toolbar',
                [
                    'toolbar' => $renderingToolbar
                ]
            );


        } catch(\Exception $e) {
            \Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\widgets\Toolbar');
            throw $e;
        }
    }

    /**
     * This method render toolbar
     *
     * @param array $items
     *
     * @throws \Exception
     *
     * @return string
     * @since  1.2
     */
    protected function buildToolbar($items)
    {
        try {
            \Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\widgets\Toolbar');
            $stringHtml = '';
            $startTag = '<li>';
            $endTag = '</li>';
            foreach($items as $item) {
                $options = $item->getParameters();
                $title = $item->getTitle();
                $urlOptions = $item->getOptions();
                $matches = [];
                if (preg_match('/(<[\w\W]+>)([\w\W]+)(<\/[\w]+>)/', $title, $matches) === 1) {
                    $startTag = $matches[1];
                    $title = $matches[2];
                    $endTag = $matches[3];
                }

                $title = str_replace('{title}', $title, $this->containerTitleHtml);
                $stringHtml .= $startTag;

                //Check Type of url
                $url = $item->getRoute();

                //Integrated url options
                if (empty($urlOptions) === false) {
                    $url = array_merge($url, $urlOptions);
                }

                $stringHtml .= Html::link(
                    $title,
                    $url,
                    $options
                );

                $stringHtml .= $this->buildToolbar($item->getChildren());

                $stringHtml .= $endTag;
            }
            //Apply template
            $stringHtml = str_replace('{toolbar}', $stringHtml, $this->htmlContainer);
            return $stringHtml;

        } catch(\Exception $e) {
            \Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), \CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar\widgets\Toolbar');
            throw $e;
        }
    }
}
