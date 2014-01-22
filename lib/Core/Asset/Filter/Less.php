<?

namespace Core\Asset\Filter;

use Appcia\Webwork\Asset\Asset;
use Appcia\Webwork\Asset\Filter;
use lessc;

class Less extends Filter
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
        $file = $asset->getSource();

        $compiler = new lessc();
        $content = $compiler->compileFile($file);
        $asset->setContent($content);

        $minifier = new Css();
        $minifier->filter($asset);

        return $this;
    }
}