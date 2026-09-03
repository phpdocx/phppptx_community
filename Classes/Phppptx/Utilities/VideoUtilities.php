<?php
namespace Phppptx\Utilities;

use Phppptx\Logger\PhppptxLogger;

/**
 * Video functions
 *
 * @category   Phppptx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class VideoUtilities
{
    /**
     * Get video content and information
     *
     * @param string $video file path, base64, stream
     * @param array $options
     *      'mime' (string) forces a mime
     * @return array
     * @throws \Exception video doesn't exist
     * @throws \Exception video format is not supported
     * @throws \Exception mime not set using a stream content
     */
    public function returnVideoContents($video, $options = array())
    {
        $videoContents = array();

        if (file_exists($video)) {
            // file content
            $extensionPath = pathinfo($video);

            $extension = strtolower($extensionPath['extension']);
            if (isset($options['mime'])) {
                $extension = $this->getExtensionFromMime($options['mime']);
            }

            $videoContents['content'] = file_get_contents($video);
            $videoContents['extension'] = $extension;
            $videoContents['mime'] = $this->getMimeFromExtension($extension);

        } else {
            // stream content
            $videoContents['content'] = file_get_contents($video);
            if (!isset($options['mime'])) {
                PhppptxLogger::logger('Stream content. Set the mime option.', 'fatal');
            }
            $videoContents['extension'] = $this->getExtensionFromMime($options['mime']);
            $videoContents['mime'] = $options['mime'];
        }

        // check if the video can be obtained
        if (!$videoContents['content']) {
            PhppptxLogger::logger('Unable to get the video.', 'fatal');
        }

        // check mime type
        if (!in_array($videoContents['mime'], array('video/mp4', 'video/unknown', 'video/x-ms-wmv', 'video/x-msvideo'))) {
            PhppptxLogger::logger('Video format \''.$videoContents['mime'].'\' is not supported.', 'fatal');
        }

        return $videoContents;
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
            case 'video/mp4':
                $extension = 'mp4';
                break;
            case 'video/unknown':
                $extension = 'mkv';
                break;
            case 'video/x-ms-wmv':
                $extension = 'wmv';
                break;
            case 'video/x-msvideo':
                $extension = 'avi';
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
            case 'avi':
                $mime = 'video/x-msvideo';
                break;
            case 'mkv':
                $mime = 'video/unknown';
                break;
            case 'mp4':
                $mime = 'video/mp4';
                break;
            case 'wmv':
                $mime = 'video/x-ms-wmv';
                break;
            default:
                break;
        }

        return strtolower($mime);
    }
}