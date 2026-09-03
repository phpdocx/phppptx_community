<?php
namespace Phppptx\Utilities;

use Phppptx\Logger\PhppptxLogger;

/**
 * Image functions
 *
 * @category   Phppptx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class ImageUtilities
{
    /**
     * Get image content and information
     *
     * @param mixed $image file path, base64, stream, GdImage
     * @param array $options
     *      'mime' (string) forces a mime
     * @return array
     * @throws \Exception image doesn't exist
     * @throws \Exception image format is not supported
     * @throws \Exception mime option is not set and getimagesizefromstring is not available
     */
    public function returnImageContents($image, $options = array())
    {
        $imageContents = array();

        if ($image instanceof \GdImage || (is_resource($image) && get_resource_type($image) == 'gd')) {
            // GdImage
            // transform to PNG
            $imageContents['extension'] = 'png';
            $imageContents['mime'] = $this->getMimeFromExtension($imageContents['extension']);
            ob_start();
            imagepng($image);
            $imageContents['content'] = ob_get_contents();
            ob_end_clean();
            $imageContents['width'] = imagesx($image);
            $imageContents['height'] = imagesy($image);

        } else if (strstr($image, 'base64,')) {
            // base64 image
            $descrArray = explode(';base64,', $image);
            $arrayExtension = explode('/', $descrArray[0]);
            $arrayMime = explode(':', $descrArray[0]);

            $imageContents['content'] = base64_decode($descrArray[1]);
            $imageContents['extension'] = strtolower($arrayExtension[1]);
            $imageContents['mime'] = $arrayMime[1];
            if (isset($options['mime'])) {
                $imageContents['mime'] = $options['mime'];
            }

            if (function_exists('getimagesizefromstring')) {
                // PHP 5.4 or newer
                $imageSize = getimagesizefromstring($imageContents['content']);
                $imageContents['width'] = $imageSize[0];
                $imageContents['height'] = $imageSize[1];
            }
        } else if (file_exists($image)) {
            // file content
            $extensionPath = pathinfo($image);

            $extension = strtolower($extensionPath['extension']);
            if (isset($options['mime'])) {
                $extension = $this->getExtensionFromMime($options['mime']);
            }

            $imageContents['content'] = file_get_contents($image);
            $imageContents['extension'] = $extension;
            $imageContents['mime'] = $this->getMimeFromExtension($extension);

            if (function_exists('getimagesizefromstring')) {
                // PHP 5.4 or newer
                $imageSize = getimagesizefromstring($imageContents['content']);
                $imageContents['width'] = $imageSize[0];
                $imageContents['height'] = $imageSize[1];
            } else {
                $imageSize = getimagesize($image);
                $imageContents['width'] = $imageSize[0];
                $imageContents['height'] = $imageSize[1];
            }
        } else {
            // stream content
            if (function_exists('getimagesizefromstring')) {
                $imageContents['content'] = file_get_contents($image);
                if ($imageContents['content'] !== false) {
                    $attrImage = getimagesizefromstring($imageContents['content']);
                    if (isset($options['mime'])) {
                        $attrImage['mime'] = $options['mime'];
                    }
                    $imageContents['extension'] = $this->getExtensionFromMime($attrImage['mime']);
                    $imageContents['mime'] = $attrImage['mime'];
                    $imageContents['width'] = $attrImage[0];
                    $imageContents['height'] = $attrImage[1];
                }
            } else {
                if (!isset($options['mime'])) {
                    PhppptxLogger::logger('getimagesizefromstring function is not available. Set the mime option.', 'fatal');
                }
            }
        }

        // check if the image can be obtained
        if (!isset($imageContents['content']) || $imageContents['content'] === false) {
            PhppptxLogger::logger('Unable to get the image.', 'fatal');
        }

        // check mime type
        if (!in_array($imageContents['mime'], array('image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'))) {
            PhppptxLogger::logger('Image format \''.$imageContents['mime'].'\' is not supported.', 'fatal');
        }

        return $imageContents;
    }


    /**
     * Gets extension from mime
     *
     * @access protected
     * @param string $mime
     * @return string
     */
    protected function getExtensionFromMime($mime)
    {
        $extension = '';

        switch ($mime) {
            case 'image/bmp':
                $extension = 'bmp';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/jpg':
            case 'image/jpeg':
                $extension = 'jpeg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/webp':
                $extension = 'webp';
                break;
            default:
                break;
        }

        return strtolower($extension);
    }

    /**
     * Gets mime from extension
     *
     * @access protected
     * @param string $extension
     * @return string
     */
    protected function getMimeFromExtension($extension)
    {
        $mime = '';

        switch ($extension) {
            case 'bmp':
                $mime = 'image/bmp';
                break;
            case 'gif':
                $mime = 'image/gif';
                break;
            case 'jpg':
                $mime = 'image/jpg';
                break;
            case 'jpeg':
                $mime = 'image/jpeg';
                break;
            case 'png':
                $mime = 'image/png';
                break;
            case 'webp':
                $mime = 'image/webp';
                break;
            default:
                break;
        }

        return strtolower($mime);
    }
}