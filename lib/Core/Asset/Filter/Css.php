<?

namespace Core\Asset\Filter;

use Appcia\Webwork\Asset\Asset;
use Appcia\Webwork\Asset\Filter;
use Minify_CSS_Compressor;

class Css extends Filter
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
        $content = $asset->getContent();
        $content = Minify_CSS_Compressor::process($content);
        $asset->setContent($content);

        return $content;
    }
}