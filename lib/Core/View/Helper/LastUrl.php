<?

namespace Core\View\Helper;

use Appcia\Webwork\View\Helper;

class LastUrl extends Helper
{
    /**
     * Caller
     *
     * @param boolean $different Last URL that differs to current
     *
     * @return string
     */
    public function lastUrl($different = true)
    {
        $tracker = $this->getView()
            ->getApp()
            ->get('core.tracker');

        $url = $different
            ? $tracker->getPreviousUrl()
            : $tracker->getLastUrl();

        return $url;
    }
}
