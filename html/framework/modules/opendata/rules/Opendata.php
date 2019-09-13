<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 22.06.2017
 * Time: 15:24
 */

declare(strict_types=1);

namespace app\modules\opendata\rules;

use app\modules\opendata\models\OpendataPassport;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UrlRule;

/**
 * Class Opendata
 *
 * @package app\modules\opendata\rules
 */
class Opendata extends UrlRule
{
    /**
     * @var array
     */
    protected static $list;

    /**
     * @var array
     */
    public $excludeRoutes = ['captcha', 'send-comment', 'rate', 'terms'];

    /**
     * @var array
     */
    protected static $routes = [
        'opendata/default/index' => 'opendata',
        'opendata/list' => 'opendata/list.<ext>',
        'opendata/list-schema' => 'opendata/list-schema.<ext>',
        'opendata/passport' => 'opendata/<inn>-<code>',
        'opendata/passport-meta' => 'opendata/<inn>-<code>/meta.<ext>',
        'opendata/passport-meta-schema' => 'opendata/<inn>-<code>/meta-schema.<ext>',
        'opendata/data' => 'opendata/<inn>-<code>/data-<data-time>-structure-<version>.<ext>',
        'opendata/data-schema' => 'opendata/<inn>-<code>/structure-<version>.<ext>',
        'opendata/set-chart' => 'opendata/set-chart/<id>',
        'opendata/set-data' => 'opendata/set-data/<id>',
        'opendata/set-atlas' => 'opendata/set-atlas/<id>',
    ];

    /**
     * @return array
     */
    protected function asList()
    {
        // todo: cache it
        if (!self::$list) {
            self::$list = OpendataPassport::find()->select([
                'code',
                'id',
            ])->indexBy('id')
                ->column();
        }

        return self::$list;
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param string $route
     * @param array $params
     *
     * @return bool|mixed|string
     */
    public function createUrl($manager, $route, $params)
    {
        if (!preg_match('#opendata#i', $route)) {
            return parent::createUrl($manager, $route, $params);
        } else {
            $params['inn'] = Yii::$app->getModule('opendata')->inn;
            $params['ext'] = ArrayHelper::getValue($params, 'ext', '');
            $params['code'] = ArrayHelper::getValue($params, 'code', '');
            $params['id'] = ArrayHelper::getValue($params, 'id', '');
            $params['data-time'] = ArrayHelper::getValue($params, 'data-time', '');
            $params['version'] = ArrayHelper::getValue($params, 'version', '');
            if ($params['code'] && !$params['id']) {
                $params['id'] = $this->codeToId($params['code']);
            }
            if ($params['id'] && !$params['code']) {
                $params['code'] = $this->idToCode($params['id']);
            }
            if (($rule = ArrayHelper::getValue(self::$routes, $route)) !== null) {
                $rule = preg_replace_callback('#\<([\w\-]+)>#i', function ($match) use ($params) {
                    return isset($params[$match[1]]) ? $params[$match[1]] : '';
                }, $rule);

                return $rule;
            }

            return false;
        }
    }

    /**
     * @param $code
     *
     * @return int|null
     */
    protected function codeToId($code)
    {
        return ArrayHelper::getValue(array_flip(self::asList()), $code);
    }

    /**
     * @param $id
     *
     * @return string|null
     */
    protected function idToCode($id)
    {
        return ArrayHelper::getValue(self::asList(), $id);
    }

    /**
     * @param \yii\web\UrlManager $manager
     * @param \yii\web\Request $request
     *
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        if (!preg_match($this->pattern, $request->getPathInfo(), $matches)) {
            return false;
        }

        /**
         * #^/opendata/7712345678-(.+?)/data-(.+?)-structure-(.+?)\.xml#        /odata/index.php    xml_mode=data&CODE=$1&data=$2&structure=$3
         * #^/opendata/7712345678-(.+?)/meta-schema\.xsd#        /odata/index.php    xml_mode=passport_structure&CODE=$1
         * #^/opendata/7712345678-(.+?)/meta\.xml#        /odata/index.php    xml_mode=passport&CODE=$1
         * #^/opendata/7712345678-(.+?)/structure-(.+?)\.xsd#        /odata/index.php    xml_mode=passport_structure&CODE=$1
         * #^/opendata/7712345678-([\w\-]+)(.*)#        /odata/index.php    xml_mode=passport_html&CODE=$1&$2
         * #^/opendata/7712345678-([a-z\d\-]+)/json(.*)#        /odata/index.php    xml_mode=data_json&CODE=$1
         * #^/opendata/7712345678-([a-z\d\-]+)/table.html(.*)#        /odata/index.php    xml_mode=data_html&CODE=$1
         * #^/opendata/list-sсhema\.xsd#        /odata/index.php    xml_mode=main_list_structure
         * #^/opendata/list\.xml#
         */
        $matches = $this->substitutePlaceholderNames($matches);

        $path = $matches['path'];

        if ($this->excludeRoutes && preg_match('#' . implode('|', $this->excludeRoutes) . '#i', $path)) {
            return false;
        }

        $params = [];
        $route = 'opendata/default/index';
        // default index page
        if (empty($path) || $path == 'index') {
            return [
                $route,
                $params,
            ];
        }
        switch (true) {
            // list of opendata sets
            case preg_match('#list\.(.+)#', $path, $parts):
                $route = 'opendata/default/list';
                $params['ext'] = $parts[1];
                break;
            case preg_match('#list-schema\.(.+)#', $path, $parts):
                $route = 'opendata/default/list-schema';
                $params['ext'] = $parts[1];
                break;
            // opendata set passport
            case preg_match('#([\d])-([\w]+)/meta\.([\w]{3,4})#', $path, $parts):
                $route = 'opendata/default/passport-meta';
                $params['code'] = $parts[2];
                $params['ext'] = $parts[3];
                $params['id'] = $this->codeToId($params['code']);
                break;
            case preg_match('#([\d])-([\w]+)/meta-schema\.([\w]{3,4})#', $path, $parts):
                $route = 'opendata/default/passport-meta-schema';
                $params['code'] = $parts[2];
                $params['ext'] = $parts[3];
                $params['id'] = $this->codeToId($params['code']);
                break;
            // html page of passport
            case preg_match('#([\d]+)-([\w]+)/?$#', $path, $parts):
                $route = 'opendata/default/passport';
                $params['code'] = $parts[2];
                $params['id'] = $this->codeToId($params['code']);
                break;
            // data of set
            case preg_match('#([\d])-([\w]+)/data-(.+?)-structure-(.+?)\.([\w]{3,4})#', $path, $parts):
                $route = 'opendata/default/data';
                $params['code'] = $parts[2];
                $params['data-time'] = $parts[3];
                $params['version'] = $parts[4];
                $params['ext'] = $parts[5];
                $params['id'] = $this->codeToId($params['code']);
                break;
            case preg_match('#([\d])-([\w]+)/structure-(.+?)\.([\w]{3,4})#', $path, $parts):
                $route = 'opendata/default/data-schema';
                $params['code'] = $parts[2];
                $params['version'] = $parts[3];
                $params['ext'] = $parts[4];
                $params['id'] = $this->codeToId($params['code']);
                break;
            case preg_match('#set-chart/(.+)#', $path, $parts):
                $route = 'opendata/default/set-chart';
                $params['id'] = $parts[1];
                break;
            case preg_match('#set-data/(.+)#', $path, $parts):
                $route = 'opendata/default/set-data';
                $params['id'] = $parts[1];
                break;
            case preg_match('#set-atlas/(.+)#', $path, $parts):
                $route = 'opendata/default/set-atlas';
                $params['id'] = $parts[1];
                break;
            // table view, graph view of opendata
        }

        return [
            $route,
            $params,
        ];
    }
}