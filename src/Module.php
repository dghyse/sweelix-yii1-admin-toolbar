<?php
/**
 * Module.php
 *
 * PHP version 5.3+
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright Copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  modules
 * @package   application.modules.toolbar
 */

namespace sweelix\yii1\admin\toolbar;

use sweelix\yii1\admin\core\components\BaseModule;
use Exception;
use Yii;

/**
 * This class manages toolbar
 *
 * @author    David Ghyse <dghyse@ibitux.com>
 * @copyright Copyright 2010-2015 Ibitux
 * @license   http://www.ibitux.com/license license
 * @version   1.4
 * @link      http://code.ibitux.net/projects/sweelix
 * @category  modules
 * @package   application.modules.toolbar
 *
 */

class Module extends BaseModule
{

    /**
     * @var string $controllerNamespace
     */
    public $controllerNamespace = 'sweelix\yii1\admin\toolbar\controllers';

    /**
     * @var string default controller name
     */
    public $defaultController = 'toolbar';


    public $pathApplicationControllers = 'application.controllers';

    /**
     * @var integer $pagerSize
     */
    public $pagerSize = 10;

    /**
     * This method init module
     *
     * @return void
     * @throws Exception
     * @since  1.2
     */
    protected function init()
    {
        try {
            Yii::trace('Trace: '.__CLASS__.'::'.__FUNCTION__.'()', 'sweelix\yii1\admin\toolbar\toolbar');
            $this->basePath = dirname(__FILE__);
            parent::init();
        } catch (Exception $e) {
            Yii::log('Error in '.__CLASS__.'::'.__FUNCTION__.'():'.$e->getMessage(), CLogger::LEVEL_ERROR, 'sweelix\yii1\admin\toolbar');
            throw $e;
        }
    }
}