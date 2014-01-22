<?

namespace Core\Model;

use Appcia\Webwork\Web\App;

class Bag
{
    /**
     * @var App
     */
    protected $app;

    /**
     * Constructor
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }
}