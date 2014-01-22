<?

namespace Core\Auth;

use Appcia\Webwork\Auth\Acl as BaseAcl;
use Appcia\Webwork\Routing\Route;
use Appcia\Webwork\Storage\Session\Space;
use Cms\Entity\Auth\User;
use Doctrine\ORM\EntityManager;

class Acl extends BaseAcl
{
    /**
     * Database handler
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em    Entity manager
     * @param Space         $space Session space
     */
    public function __construct(EntityManager $em, Space $space)
    {
        parent::__construct($space);

        $this->em = $em;
    }

    /**
     * Verify that user has access to specific group
     *
     * @param Route $route Route
     *
     * @return bool
     */
    protected function verifyCustom($route)
    {
        $user = $this->getUser();

        foreach ($user->getGroupIds() as $groupId) {
            if ($this->verifyRoute($route, 'group-' . $groupId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save user ID in session
     *
     * @param User $user Authorized user
     *
     * @return int
     */
    protected function sleepUser($user)
    {
        $id = $user->getId();

        return $id;
    }

    /**
     * @return User\Repository
     */
    protected function getUserRepository()
    {
        return $this->em->getRepository('Cms\Entity\Auth\User');
    }

    /**
     * Load user basing on ID stored in session
     *
     * @param int $userId User ID
     *
     * @return User
     */
    protected function wakeupUser($userId)
    {
        $user = $this->getUserRepository()
            ->getById($userId);

        return $user;
    }
}