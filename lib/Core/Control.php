<?

namespace Core;

use Appcia\Webwork\Auth\Auth;
use Appcia\Webwork\Control\Fat;
use Appcia\Webwork\Resource;
use Appcia\Webwork\Util\Logger;
use Cms\Entity\Auth\User;
use Cms\Entity\System\Locale;
use Cms\Entity\System\Setting;

class Control extends Fat
{
    /**
     * Go to previously tracked URL
     */
    public function goBack($defaultUrl = null)
    {
        $url = $this->get('core.tracker')
            ->getPreviousUrl();

        if ($url === null) {
            $url = $defaultUrl;
        }

        $this->goRedirect($url);
    }

    /**
     * Get currently authorized user
     *
     * @return Auth
     */
    public function getAuth()
    {
        return $this->get('core.auth');
    }

    /**
     * @return User
     */
    public function getCurrentUser()
    {
        return $this->getAuth()
            ->getUser();
    }

    /**
     * @return Logger
     */
    public function getLog()
    {
        return $this->get('core.log');
    }

    /**
     * Get locale data
     *
     * @return Locale
     */
    public function getCurrentLocale()
    {
        return $this->get('core.locale');
    }

    /**
     * @return Resource\Manager
     */
    public function getResourceManager()
    {
        return $this->get('core.resource.manager');
    }

    /**
     * @return Entity\Manager
     */
    public function getEntityManager()
    {
        return $this->get('core.entity.manager');
    }
}