<?

namespace Core\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @method Manager getEntityManager()
 */
abstract class Repository extends EntityRepository
{
    /**
     * Cache control
     *
     * @var bool
     */
    protected $cacheEnabled;

    /**
     * Cache lifetime
     *
     * @var int
     */
    protected $cacheLifetime;

    /**
     * Constructor
     *
     * @param Manager       $em    Entity manager
     * @param ClassMetadata $class Class descriptor.
     */
    public function __construct(Manager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->cacheLifetime = null;
        $this->cacheEnabled = true;
    }

    /**
     * Set cache control state
     *
     * @param bool $flag
     *
     * @return Repository
     */
    public function setCacheEnabled($flag)
    {
        $this->cacheEnabled = $flag;

        return $this;
    }

    /**
     * Get cache control state
     *
     * @return boolean
     */
    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * Get cache lifetime
     *
     * @return int
     */
    public function getCacheLifetime()
    {
        return $this->cacheLifetime;
    }

    /**
     * Set cache lifetime
     *
     * @param int $seconds
     *
     * @return Helper
     */
    public function setCacheLifetime($seconds)
    {
        $this->cacheLifetime = $seconds;

        return $this;
    }

    /**
     * Get entities by set of identifiers
     *
     * @param array $ids Identifiers
     *
     * @return array
     */
    public function findById(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $query = $this->createQueryBuilder('e')
            ->where('e.id IN (:id)')
            ->setParameter('id', $ids)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get entity by identifier
     *
     * @param $id
     *
     * @return object|null
     */
    public function getById($id)
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->useResultCache(
                $this->cacheEnabled,
                $this->cacheLifetime,
                $this->composeCacheKey($id)
            );

        $entity = $query->getOneOrNullResult();

        return $entity;
    }

    /**
     * Compose unique cache key
     *
     * @param mixed $id     Identifier
     * @param mixed $params Parameters
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function composeCacheKey($id, $params = null)
    {
        $key = $this->_entityName . '|';

        if (is_array($id)) {
            $params = $id;
        } elseif (is_scalar($id)) {
            $key .= $id;
        } else {
            throw new \InvalidArgumentException("Cache key identifier should be a scalar value.");
        }

        if ($params !== null) {
            if (is_array($params)) {
                $params = implode('-', $params);
            } elseif (!is_scalar($params)) {
                throw new \InvalidArgumentException("Cache key parameter should be a scalar or array.");
            }

            $key .= '-' . $params;

        }

        return $key;
    }

    /**
     * Clean cache by entity identifier
     *
     * @param $id
     *
     * @return Repository
     */
    public function cleanById($id)
    {
        $this->cleanResultCache($this->composeCacheKey($id));

        return $this;
    }

    /**
     * Shortcut for cleaning result cache
     *
     * @param string $key Cache key
     *
     * @return Repository
     */
    public function cleanResultCache($key)
    {
        $this->getEntityManager()
            ->getConfiguration()
            ->getResultCacheImpl()
            ->delete($key);

        return $this;
    }
}