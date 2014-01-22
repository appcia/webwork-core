<?

namespace Core\Cli;

use Core\Cli\Command;
use Appcia\Webwork\Web\App as WebApp;
use Symfony\Component\Console\Application;

class App extends Application
{
    /**
     * @var WebApp
     */
    private $webApp;

    /**
     * Constructor.
     */
    public function __construct(WebApp $app) {
        parent::__construct('Careme CLI', '0.1');

        $this->webApp = $app;

        $this->addCommands(array(
            new Command\Fixture(),
            new Command\CacheClear()
        ));
    }

    /**
     * Get web application
     *
     * @return WebApp
     */
    public function getWebApp()
    {
        return $this->webApp;
    }
}