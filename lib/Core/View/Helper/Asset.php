<?

namespace Core\View\Helper;

use Appcia\Webwork\View\Helper;
use Appcia\Webwork\Asset\Asset as Model;
use Appcia\Webwork\Asset\Manager;

class Asset extends Helper
{
    /**
     * @return Manager
     */
    protected function getManager()
    {
        return $this->view->getApp()
            ->get('core.am');
    }

    /**
     * Create asset from path
     *
     * @param string $path
     *
     * @return Asset
     */
    public function asset($path)
    {
        $asset = new Model($this->getManager(), $path);

        return $asset;
    }
}