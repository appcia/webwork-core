<?

namespace Core;

use Appcia\Webwork\System\File;
use Doctrine\Common\DataFixtures\AbstractFixture;

abstract class Fixture extends AbstractFixture
{
    protected function locate($file = null)
    {
        $reflector = new \ReflectionClass($this);
        $path = dirname($reflector->getFileName());

        if ($file !== null) {
            $path .= '/' . $file;
        }

        return $path;
    }

    protected function read($file)
    {
        $file = new File($this->locate($file));
        $content = $file->read();

        return $content;
    }
}