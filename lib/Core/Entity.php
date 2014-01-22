<?

namespace Core;

use Appcia\Webwork\Core\Locator;
use Core\Entity\Descriptor;
use Core\Entity\Manager;

abstract class Entity
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Get useful descriptor (for route names, displaying in views)
     *
     * @return Descriptor
     */
    public function getDescriptor()
    {
        $class = get_class($this);
        $descriptor = Locator::get('core.entity.manager')
            ->getDescriptor($class);

        return $descriptor;
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return Locator::get('core.entity.manager');
    }

    /**
     * @param Entity $entity
     * @param string $name
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function property($entity, $name)
    {
        $prop = null;
        if (!$entity instanceof Entity) {
            throw new \InvalidArgumentException(sprintf(
                "Entity '%s' has invalid type: '%s'.",
                get_class($entity),
                gettype($entity)
            ));
        }

        if (is_callable($name)) {
            $prop = $name($entity);
        } else {
            foreach (array('get', 'is') as $prefix) {
                $getter = $prefix . ucfirst($name);
                if (method_exists($entity, $getter)) {
                    $prop = $entity->{$getter}();
                    break;
                }

                throw new \InvalidArgumentException(sprintf(
                    "Entity '%s' has no callable getter for property '%s'.",
                    get_class($entity),
                    $name
                ));
            }
        }

        return $prop;
    }

    /**
     * Property extractor
     *
     * @param Entity[] $entities
     * @param string   $props
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function map($entities, $props)
    {
        $res = array();

        if (is_array($props)) {
            foreach ($entities as $entity) {
                $sub = array();
                foreach ($props as $prop) {
                    $sub[$prop] = static::property($entity, $prop);
                }

                $res[] = $sub;
            }
        } else {
            foreach ($entities as $entity) {
                $res[] = self::property($entity, $props);
            }
        }

        return $res;
    }
}