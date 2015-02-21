<?php namespace Wieni\Image;

use FilesystemIterator;
use Intervention\Image\ImageManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    /**
     * @var string
     */
    protected $imagePath;
    /**
     * @var Image
     */
    protected $image;
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @param Image $image
     * @param ImageManager $imageManager
     */
    function __construct(Image $image, ImageManager $imageManager)
    {
        $this->image = $image;
        $this->imageManager = $imageManager;
        $this->imagePath = config('image.path');
    }

    /**
     * @param UploadedFile $file
     * @param array $data
     * @return Image
     */
    public function createFromUploadedFile(UploadedFile $file, array $data = [])
    {
        $filename = str_random() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $file->move($this->imagePath, $filename);

        return $this->create(['filename' => $filename] + $data);
    }

    /**
     * @param array $data
     * @return Image
     */
    protected function create(array $data)
    {
        list($width, $height) = getimagesize($this->imagePath . '/' . $data['filename']);

        return $this->image->create($data + ['width' => $width, 'height' => $height]);
    }

    public function createMultiple(array $images)
    {

    }

    /**
     * @param Image $image
     * @param array $formats
     * @return array
     */
    public function createFormats(Image $image, array $formats)
    {
        $results = [];

        foreach ($formats as $format) {
            $results[$format] = $this->createFormat($image, $format);
        }

        return $results;
    }

    public function createFormat(Image $image, $format)
    {
        $class = '\App\Image\\' . studly_case($format) . 'ImageStyle';

        if (class_exists($class)) {
            $imageStyle = new $class($image);
            $imageStyle->run();

            return true;
        }

        return false;
    }

    public function destroy(Image $image)
    {
        // Delete all occurrences of the image file in the image directory
        // todo: use regular expression
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->imagePath, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS));

        foreach ($files as $filePath => $file) {
            $filePathPieces = explode('/', $filePath);
            $filename = array_pop($filePathPieces);
            if ($filename == $image->filename) {
                unlink($filePath);
            }
        }

        // Remove the image from db
        $image->delete();
    }
}
