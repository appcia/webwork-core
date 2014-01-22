<?

namespace Core\Asset\Filter;

use Appcia\Webwork\Asset\Asset;
use Appcia\Webwork\Asset\Filter;
use scssc;

class Scss extends Filter
{
    /**
     * @{@inheritdoc}
     */
    public function prepare(Asset $asset)
    {
        $asset->getTarget()
            ->setExtension('css');

        return $this;
    }

    /**
     * @{@inheritdoc}
     */
    public function filter(Asset $asset)
    {
        $source = $asset->getSource();

        $compiler = new scssc();
        $path = $source->getDir()->getAbsolutePath();
        $compiler->addImportPath($path);
        $content = $compiler->compile('@import "'.$source->getAbsolutePath().'"');
        $asset->setContent($content);

        $minifier = new Css();
        $minifier->filter($asset);

        return $this;
    }
}