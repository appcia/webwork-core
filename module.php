<?

namespace Core;

use Appcia\Webwork\Asset;
use Appcia\Webwork\Core\Module;
use Appcia\Webwork\Resource;
use Appcia\Webwork\Storage\Config;
use Appcia\Webwork\Storage\Session;
use Appcia\Webwork\Web\App;
use Appcia\Webwork\Web\Dispatcher;
use Appcia\Webwork\Web\Tracker;
use Cms\Entity\Auth\Group;
use Cms\Entity\System\Setting;
use Core\Auth\Acl;
use Core\Entity;
use Core\Intl\Locale\Switcher;

/**
 * @property App app
 */
class CoreModule extends Module
{
    /**
     * @return $this
     */
    public function init()
    {
        $that = $this;
        $dispatcher = $this->app->getDispatcher();
        $monitor = $dispatcher->getMonitor();

        // Configuration
        $this->app->getConfig()
            ->load($this->getPath() . '/config/settings.php')
            ->load($this->getPath() . '/config/' . $this->app->getEnvironment() . '/settings.php');

        // Auth
        $this->app->single('core.auth', function () use ($that) {
            $em = $that->getApp()
                ->get('core.entity.manager');
            $session = $that->getApp()
                ->get('core.session');
            $space = new Session\Space($session, 'core.auth');

            $auth = new Acl($em, $space);
            $that->app->getConfig()
                ->grab('auth')
                ->inject($auth);

            return $auth;
        });

        // Data cache
        $this->app->single('core.cache.data', function () use ($that) {
            $config = $that->getApp()
                ->getConfig()
                ->grab('cache.data');
            $cache = $that->getCache($config);

            return $cache;
        });

        // Accelerator cache
        $this->app->single('core.cache.accelerator', function () use ($that) {
            $config = $that->getApp()
                ->getConfig()
                ->grab('cache.accelerator');
            $cache = $that->getCache($config);

            return $cache;
        });

        // Entity manager
        $this->app->single('core.entity.manager', function () use ($that) {
            $config = $that->getApp()
                ->getConfig();
            $dataCache = $that->getApp()
                ->get('core.cache.data');
            $acceleratorCache = $that->getApp()
                ->get('core.cache.accelerator');

            $cfg = new \Doctrine\ORM\Configuration();
            $cfg->setResultCacheImpl($dataCache);
            $cfg->setMetadataCacheImpl($acceleratorCache);
            $cfg->setQueryCacheImpl($acceleratorCache);
            $cfg->setProxyDir($config->get('cache.proxyDir'));
            $cfg->setProxyNamespace('Proxies');
            $cfg->setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());

            $paths = $that->getApp()
                ->findPaths('Entity');
            $driverImpl = $cfg->newDefaultAnnotationDriver($paths);
            $cfg->setMetadataDriverImpl($driverImpl);

            $conn = \Doctrine\DBAL\DriverManager::getConnection(
                $config->get('db')->getData(), $cfg, new \Doctrine\Common\EventManager()
            );
            $em = new Entity\Manager($that->getApp(), $conn, $cfg, $conn->getEventManager());

            return $em;
        });

        // Session
        $this->app->single('core.session', function () use ($that) {
            $session = new Session();
            $that->getApp()
                ->getConfig()
                ->grab('session')
                ->inject($session);

            return $session;
        });

        // Resource manager
        $this->app->single('core.resource.manager', function () use ($that) {
            $rm = new Resource\Manager();
            $that->getApp()
                ->getConfig()
                ->grab('resource.manager')
                ->inject($rm);

            return $rm;
        });

        // Asset manager
        $this->app->single('core.am', function () use ($that) {
            $am = new Asset\Manager($that->getApp());
            $that->getApp()
                ->getConfig()
                ->grab('asset.manager')
                ->inject($am);

            return $am;
        });

        // Browser console output
        $this->app->single('core.console', function () {
            $log = new \Monolog\Logger('console');
            $log->pushHandler(new \Monolog\Handler\ChromePHPHandler());
            $log->pushHandler(new \Monolog\Handler\FirePHPHandler());

            return $log;
        });

        // Request tracker
        $this->app->single('core.tracker', function () use ($that) {
            $request = $that->getApp()
                ->getRequest();
            $session = $that->getApp()
                ->get('core.session');
            $space = new Session\Space($session, 'core.tracker');

            $tracker = new Tracker($space, $request);
            $that->getApp()
                ->getConfig()
                ->grab('tracker')
                ->inject($tracker);

            return $tracker;
        });

        // Logger
        $this->app->single('core.log', function () use ($that) {
            $config = $that->getApp()
                ->getConfig();
            $env = $that->getApp()
                ->getEnvironment();

            $log = new \Monolog\Logger('log');
            $log->pushHandler(new \Monolog\Handler\StreamHandler($config->get('log.app')));
            $log->pushProcessor(new \Monolog\Processor\WebProcessor());
            $log->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
            $log->pushProcessor(new \Monolog\Processor\IntrospectionProcessor());

            if (in_array($env, array(App::DEVELOPMENT))) {
                $log->pushHandler(new \Monolog\Handler\ChromePHPHandler());
                $log->pushHandler(new \Monolog\Handler\FirePHPHandler());
            }

            return $log;
        });

        // Locale
        $this->app->set('core.locale', function () use ($that) {
            $code = $that->getApp()
                ->getContext()
                ->getLocale()
                ->getActive();

            $locale = $that->getApp()
                ->get('core.entity.manager')
                ->getRepository('Cms\Entity\System\Locale')
                ->findOneByCode($code);

            return $locale;
        });

        $this->app->set('core.locale.switcher', function () use ($that) {
            $locale = $that->getApp()
                ->getContext()
                ->getLocale();
            $session = $that->getApp()
                ->get('core.session');
            $space = new Session\Space($session, 'core.locale.switcher');

            $switcher = new Switcher($locale, $space);
            $that->getApp()
                ->getConfig()
                ->grab('locale.switcher')
                ->inject($switcher);

            return $switcher;
        });

        // Dispatcher events
        $monitor->listen(Dispatcher::STARTED, function () use ($that) {
            $switcher = $that->getApp()
                ->get('core.locale.switcher');
            $switcher->remember();
        });

        $monitor->listen(Dispatcher::ROUTE_FOUND, function () use ($that) {
            $dispatcher = $that->getApp()
                ->getDispatcher();
            $route = $dispatcher->getRoute();
            $auth = $that->getApp()
                ->get('core.auth');

            if (!$auth->isAccessible($route)) {
                $dispatcher->forceRoute('site-error-unauthorized');
            }
        });

        $monitor->listen(Dispatcher::ENDED, function () use ($that) {
            $app = $that->getApp();
            $tracker = $that->getApp()
                ->get('core.tracker');
            $tracker->track();

            if ($that->getApp()->getEnvironment() == App::DEVELOPMENT) {
                $em = $app->get('core.entity.manager');
                $console = $app->get('core.console');

                $sql = $em->getConfiguration()
                    ->getSQLLogger();

                foreach ($sql->queries as $query) {
                    $data = array(
                        'params' => $query['params'],
                        'types' => $query['types']
                    );

                    $console->debug($query['sql'], $data);
                }
            }
        });

        return $this;
    }

    /**
     * @param Config $config
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     * @throws \OutOfBoundsException
     */
    public function getCache($config)
    {
        $type = $config->get('type');

        switch ($type) {
        case 'memcache':
            $memcache = new \Memcache();
            $memcache->connect(
                $config->get('config.host'),
                $config->get('config.port')
            );

            $cache = new \Doctrine\Common\Cache\MemcacheCache();
            $cache->setMemcache($memcache);
            break;
        case 'xcache':
            $cache = new \Doctrine\Common\Cache\XCacheCache();
            break;
        case 'apc':
            $cache = new \Doctrine\Common\Cache\ApcCache();
            break;
        case 'array':
            $cache = new \Doctrine\Common\Cache\ArrayCache();
            break;
        default:
            throw new \OutOfBoundsException(sprintf("Unsupported cache type: '%s'", $type));
            break;
        }

        return $cache;
    }

    /**
     * @return $this
     */
    public function run()
    {
        return $this;
    }

}