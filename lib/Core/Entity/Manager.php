<?

namespace Core\Entity;

use Appcia\Webwork\Core\App;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;

class Manager extends EntityManager
{
    /**
     * Application
     *
     * @var App
     */
    protected $app;

    /**
     * Not inherited from parent class (private)
     *
     * @var array
     */
    protected $repositories = array();

    /**
     * Additional data associated with entities
     *
     * @var array
     */
    protected $descriptors = array();

    /**
     * Constructor
     *
     * @param App           $app
     * @param Connection    $conn
     * @param Configuration $config
     * @param EventManager  $eventManager
     */
    public function __construct(App $app, Connection $conn, Configuration $config, EventManager $eventManager)
    {
        parent::__construct($conn, $config, $eventManager);

        $this->app = $app;
    }

    /**
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param string $entity Entity name
     *
     * @return Manager
     */
    public function getRepository($entity)
    {
        if (isset($this->repositories[$entity])) {
            return $this->repositories[$entity];
        }

        $repository = parent::getRepository($entity);

        $this->app->getConfig()
            ->grab('entity.manager.repository.' . $entity)
            ->inject($repository);

        $this->repositories[$entity] = $repository;

        return $repository;
    }

    /**
     * @param string $entity Entity name
     *
     * @return Descriptor
     */
    public function getDescriptor($entity)
    {
        if (isset($this->descriptors[$entity])) {
            return $this->descriptors[$entity];
        }

        $config = $this->app->getConfig()
            ->grab('entity.manager.descriptor.' . $entity);
        $descriptor = new Descriptor($config->getData(), $entity);

        $this->descriptors[$entity] = $descriptor;

        return $descriptor;
    }
}