<?

namespace Core\Resource\Processor;

use Appcia\Webwork\Resource\Service\Processor;
use Appcia\Webwork\Resource\Type;
use Gregwar\Image\Image;

class Thumbnail extends Processor
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var string
     */
    protected $background;

    /**
     * @var boolean
     */
    protected $zoom;

    /**
     * {@inheritdoc}
     */
    public function __construct(Type $type)
    {
        parent::__construct($type);

        $this->width = 128;
        $this->height = 128;
        $this->background = 0x000000;
        $this->zoom = false;
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = max(0, $height);

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = max(0, $width);

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $background
     */
    public function setBackground($background)
    {
        $this->background = $background;
    }

    /**
     * @return string
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @param boolean $zoom
     */
    public function setZoom($zoom)
    {
        $this->zoom = (bool) $zoom;
    }

    /**
     * @return boolean
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * {@inheritdoc}
     */
    public function call()
    {
        $source = $this->type->getResource()
            ->getFile();
        $target = $this->type->getFile(false);
        $temp = $this->getManager()
            ->getTempDir()
            ->randFile($source->getExtension());

        $image = Image::open($source->getPath());
        if ($this->zoom) {
            $image->zoomCrop($this->width, $this->height, $this->background);
        } else {
            $image->resize($this->width, $this->height, $this->background);
        }
        $image->save($temp->getPath());

        $temp->move($target);

        return $temp;
    }
}