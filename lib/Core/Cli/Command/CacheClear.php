<?

namespace Core\Cli\Command;

use Appcia\Webwork\Web\Request;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CacheClear extends Console\Command\Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cache:clear')
            ->setDescription('Clear accelerator cache by request to apache')
            ->addArgument('what', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $params = array();
        $what = $input->getArgument('what');

        if ($what !== null) {
            $params['what'] = $what;
        }

        $app = $this->getApplication()
            ->getWebApp();

        $domain = $context = $app->getContext()
            ->getDomain();
        $path = $app->getRouter()
            ->assemble('site-script-clear-cache', $params);
        $url = 'http://' . $domain . $path;

        $data = file_get_contents($url);
        $data = strip_tags($data);

        $output->writeln($data);
    }
}