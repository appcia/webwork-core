<?

namespace Core\Entity;

use Appcia\Webwork\Storage\Config;

/**
 * Entity descriptor
 * Additional data holder (for routes, views)
 */
class Descriptor extends Config
{
    /**
     * Described entity name
     *
     * @var string
     */
    protected $entity;

    /**
     * Constructor
     *
     * @param string $entity
     * @param array  $data
     */
    public function __construct($data = array(), $entity = null)
    {
        parent::__construct($data);

        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }
}