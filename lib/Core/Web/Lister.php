<?

namespace Core\Web;

use Appcia\Webwork\Web\Lister as Base;
use Core\Entity\Repository;
use Doctrine\ORM\QueryBuilder;

class Lister extends Base
{
    const ALIAS = '_';

    /**
     * Repository from which elements will be taken
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Static filters
     *
     * @var array
     */
    protected $criteria;

    /**
     * Constructor
     *
     * @param Repository $repository Repository for getting entities
     * @param array      $criteria   Static filters
     */
    public function __construct(Repository $repository, $criteria = array())
    {
        parent::__construct();

        $this->repository = $repository;
        $this->criteria = $criteria;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchElements()
    {
        $qb = $this->repository->createQueryBuilder(static::ALIAS);
        $this->applyFilters($qb, $this->criteria);

        $this->applyFilters($qb, $this->getFilters());
        $this->applySorters($qb, $this->getSorters());

        $qb->setFirstResult($this->pagination->getOffset())
            ->setMaxResults($this->pagination->getPerPage());

        $query = $qb->getQuery();
        $elements = $query->getResult();

        return $elements;
    }

    /**
     * @param string $column
     *
     * @return string
     */
    protected function aliasColumn($column)
    {
        if (strpos($column, '.') === false) {
            $column = static::ALIAS . '.' . $column;
        }

        return $column;
    }

    /**
     * @param QueryBuilder $qb
     * @param mixed        $filters
     *
     * @return $this
     */
    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        foreach ($filters as $column => $value) {
            $alias = $this->aliasColumn($column);

            if (is_callable($value)) {
                $value($qb);
            } elseif (is_object($value)) {
                $qb->andWhere("{$alias} = :{$column}")
                    ->setParameter($column, $value);
            } else {
                $qb->andWhere("{$alias} LIKE :{$column}")
                    ->setParameter($column, "%{$value}%");
            }
        }

        return $this;
    }

    /**
     * @param QueryBuilder $qb
     * @param mixed        $sorters
     *
     * @return $this
     */
    protected function applySorters(QueryBuilder $qb, $sorters)
    {
        foreach ($sorters as $column => $dir) {
            $alias = $this->aliasColumn($column);

            if (is_callable($dir)) {
                $dir($qb);
            } else {
                $qb->addOrderBy($alias, $dir);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function countElements()
    {
        $qb = $this->repository->createQueryBuilder(static::ALIAS);
        $qb->select($qb->expr()->count($this->aliasColumn('id')));

        $this->applyFilters($qb, $this->criteria);

        $query = $qb->getQuery();
        $res = $query->getOneOrNullResult();

        $count = !empty($res)
            ? (int) $res[1]
            : 0;

        return $count;
    }
}