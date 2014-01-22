<?

namespace Core\Intl\Locale;

use Appcia\Webwork\Intl\Locale;
use Appcia\Webwork\Storage\Session\Space;

class Switcher
{
    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var Space
     */
    protected $data;

    /**
     * Constructor
     *
     * @param Locale $locale Locale to be modified
     * @param Space  $space  Data storage
     */
    public function __construct(Locale $locale, Space $space)
    {
        $this->locale = $locale;
        $this->data = $space;
    }

    /**
     * Remember changed locale
     *
     * @return $this
     */
    public function remember()
    {
        if (!isset($this->data['active'])) {
            $this->data['active'] = $this->locale->getActive();
        } else {
            $this->locale->setActive($this->data['active']);
        }

        return $this;
    }

    /**
     * Forget changed locale
     *
     * @return $this
     */
    public function forget()
    {
        unset($this->data['active']);

        return $this;
    }

    /**
     * Change current locale
     *
     * @param string $code Locale code 'en_US' etc
     *
     * @return $this
     */
    public function change($code)
    {
        $this->locale->setActive($code);
        $this->data['active'] = $this->locale->getActive();

        return $this;
    }
}