<?

namespace Core\Data\Filter;

use Appcia\Webwork\Core\Locator;
use Appcia\Webwork\Data\Component\Filter\Slug as BaseSlug;
use Appcia\Webwork\Web\Context;

class Slug extends BaseSlug
{

    /**
     * @var string
     */
    private $entity;

    /**
     * Constructor
     *
     * @param Context $context Use context
     * @param string  $class   Entity class
     */
    public function __construct(Context $context, $class)
    {
        parent::__construct($context);

        $this->entity = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $value = parent::filter($value);
        $value = $this->numerize($value);

        return $value;
    }

    /**
     * Avoid duplicates, append numbers to value
     *
     * @param string $value Value
     *
     * @return string
     */
    private function numerize($value)
    {
        $dql = sprintf("SELECT u.slug FROM %s u WHERE u.slug like '%s%%'", $this->entity, $value);
        $query = Locator::get('core.entity.manager')->createQuery($dql);
        $rows = $query->getResult();

        $slug = $value;
        $no = 1;

        do {
            $found = false;
            foreach ($rows as $row) {
                if ($slug == $row['slug']) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $slug = $value . '-' . $no;
                $no++;
            }
        } while ($found);

        return $slug;
    }
}