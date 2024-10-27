<?php
/**
 * Created by PhpStorm.
 * User: PhatPNH
 * Date: 22/06/2024
 * Time: 7:53 CH
 */


namespace fat2fast\auth\components;

use fat2fast\admin\models\Route;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\web\User;

class Helper
{
    const CACHE_TAG = 'fat2fast.admin.route';
    private static $strict = true;
    private static $onlyRegisteredRoute = false;

    private static $_userRoutes = [];
    private static $_defaultRoutes;
    private static $_routes;

    public static function getRegisteredRoutes()
    {
        if (self::$_routes === null) {
            self::$_routes = [];
            $manager = Yii::$app->getAuthManager();
            foreach ($manager->getPermissions() as $item) {
                if ($item->name[0] === '/') {
                    self::$_routes[$item->name] = $item->name;
                }
            }
        }
        return self::$_routes;
    }

    /**
     * Get assigned routes by default roles
     * @return array
     */
    protected static function getDefaultRoutes()
    {
        if (self::$_defaultRoutes === null) {
            $manager = Yii::$app->getAuthManager();
            $roles = $manager->defaultRoles;
            $cache = Yii::$app->cache;
            if ($cache && ($routes = $cache->get($roles)) !== false) {
                self::$_defaultRoutes = $routes;
            } else {
                $permissions = self::$_defaultRoutes = [];
                foreach ($roles as $role) {
                    $permissions = array_merge($permissions, $manager->getPermissionsByRole($role));
                }
                foreach ($permissions as $item) {
                    if ($item->name[0] === '/') {
                        self::$_defaultRoutes[$item->name] = true;
                    }
                }
                if ($cache !== null) {
                    $cache->set($roles, self::$_defaultRoutes, 0, new TagDependency([
                        'tags' => self::CACHE_TAG
                    ]));
                }
            }
        }
        return self::$_defaultRoutes;
    }

    /**
     * Get assigned routes of user.
     * @param integer $userId
     * @return array
     */
    public static function getRoutesByUser($userId)
    {
        if (!isset(self::$_userRoutes[$userId])) {
            $cache = Yii::$app->cache;
            if ($cache && ($routes = $cache->get([__METHOD__, $userId])) !== false) {
                self::$_userRoutes[$userId] = $routes;
            } else {
                $routes = static::getDefaultRoutes();
                $manager = Yii::$app->getAuthManager();
                foreach ($manager->getPermissionsByUser($userId) as $item) {
                    if ($item->name[0] === '/') {
                        $routes[$item->name] = true;
                    }
                }
                self::$_userRoutes[$userId] = $routes;
                if ($cache !== null) {
                    $cache->set([__METHOD__, $userId], $routes, 0, new TagDependency([
                        'tags' => self::CACHE_TAG
                    ]));
                }
            }
        }
        return self::$_userRoutes[$userId];
    }

    /**
     * Check access route for user.
     * @param string|array $route
     * @param integer|User $user
     * @return boolean
     */
    public static function checkRoute($route, $params = [], $user = null)
    {
        $r = static::normalizeRoute($route);
        if (self::$onlyRegisteredRoute && !isset(static::getRegisteredRoutes()[$r])) {
            return true;
        }

        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        $userId = $user instanceof User ? $user->getId() : $user;

        if (self::$strict) {
            if ($user->can($r, $params)) {
                return true;
            }
            while (($pos = strrpos($r, '/')) > 0) {
                $r = substr($r, 0, $pos);
                if ($user->can($r . '/*', $params)) {
                    return true;
                }
            }
            return $user->can('/*', $params);
        } else {
            $routes = static::getRoutesByUser($userId);
            if (isset($routes[$r])) {
                return true;
            }
            while (($pos = strrpos($r, '/')) > 0) {
                $r = substr($r, 0, $pos);
                if (isset($routes[$r . '/*'])) {
                    return true;
                }
            }
            return isset($routes['/*']);
        }
    }

    /**
     * Normalize route
     * @param string $route Plain route string
     * @param boolean|array $advanced Array containing the advanced configuration. Defaults to false.
     * @return string            Normalized route string
     */
    protected static function normalizeRoute($route, $advanced = false)
    {
        if ($route === '') {
            $normalized = '/' . Yii::$app->controller->getRoute();
        } elseif (strncmp($route, '/', 1) === 0) {
            $normalized = $route;
        } elseif (strpos($route, '/') === false) {
            $normalized = '/' . Yii::$app->controller->getUniqueId() . '/' . $route;
        } elseif (($mid = Yii::$app->controller->module->getUniqueId()) !== '') {
            $normalized = '/' . $mid . '/' . $route;
        } else {
            $normalized = '/' . $route;
        }
        // Prefix @app-id to route.
        if ($advanced) {
            $normalized = Route::PREFIX_ADVANCED . Yii::$app->id . $normalized;
        }
        return $normalized;
    }

    /**
     * Filter menu items
     * @param array $items
     * @param integer|User $user
     */
    public static function filter($items, $user = null)
    {
        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        return static::filterRecursive($items, $user);
    }

    /**
     * Filter menu recursive
     * @param array $items
     * @param integer|User $user
     * @return array
     */
    protected static function filterRecursive($items, $user)
    {
        $result = [];
        foreach ($items as $i => $item) {
            $url = ArrayHelper::getValue($item, 'url', '#');
            $allow = is_array($url) ? static::checkRoute($url[0], array_slice($url, 1), $user) : true;

            if (isset($item['items']) && is_array($item['items'])) {
                $subItems = self::filterRecursive($item['items'], $user);
                if (count($subItems)) {
                    $allow = true;
                }
                $item['items'] = $subItems;
            }
            if ($allow && !($url == '#' && empty($item['items']))) {
                $result[$i] = $item;
            }
        }
        return $result;
    }

    /**
     * Filter action column button. Use with [[yii\grid\GridView]]
     * ```php
     * 'columns' => [
     *     ...
     *     [
     *         'class' => 'yii\grid\ActionColumn',
     *         'template' => Helper::filterActionColumn(['view','update','activate'])
     *     ]
     * ],
     * ```
     * @param array|string $buttons
     * @param integer|User $user
     * @return string
     */
    public static function filterActionColumn($buttons = [], $user = null)
    {
        if (is_array($buttons)) {
            $result = [];
            foreach ($buttons as $button) {
                if (static::checkRoute($button, [], $user)) {
                    $result[] = "{{$button}}";
                }
            }
            return implode(' ', $result);
        }
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($user) {
            return static::checkRoute($matches[1], [], $user) ? "{{$matches[1]}}" : '';
        }, $buttons);
    }

    /**
     * Use to invalidate cache.
     */
    public static function invalidate()
    {
        if (Yii::$app->cache !== null) {
            TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG);
        }
    }
}
