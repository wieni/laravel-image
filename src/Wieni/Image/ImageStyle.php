<?php namespace Wieni\Image;

use Intervention\Image\ImageManager;

abstract class ImageStyle
{
    /**
     * @var string
     */
    protected $imagePath;
    /**
     * @var string
     */
    protected $folder;
    /**
     * @var ImageManager
     */
    protected $imageManager;
    /**
     * @var Image
     */
    protected $image;


    /**
     * Constructor
     * @param Image $image
     */
    function __construct(Image $image)
    {
        $this->imageManager = new ImageManager();
        $this->image = $image;
        $this->imagePath = config('image.path');
    }

    public function run()
    {
        if (!file_exists($this->imagePath . '/' . $this->folder)) {
            mkdir($this->imagePath . '/' . $this->folder, 0777, true);
        }

        $this->execute();
    }

    abstract protected function execute();
}
