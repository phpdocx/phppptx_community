<?php
namespace Phppptx\Utilities;

use Phppptx\Logger\PhppptxLogger;

/**
 * Audio functions
 *
 * @category   Phppptx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Labs SL
 *             (https://www.narcealabs.com)
 * @license    phppptx Community License
 * @link       https://www.phppptx.com
 */
class AudioUtilities
{
    /**
     * Get audio content and information
     *
     * @param string $audio file path, stream
     * @param array $options
     *      'mime' (string) forces a mime
     * @return array
     * @throws \Exception audio doesn't exist
     * @throws \Exception audio format is not supported
     * @throws \Exception mime not set using a stream content
     */
    public function returnAudioContents($audio, $options = array())
    {
        $audioContents = array();

        if (file_exists($audio)) {
            // file content
            $extensionPath = pathinfo($audio);

            $extension = strtolower($extensionPath['extension']);
            if (isset($options['mime'])) {
                $extension = $this->getExtensionFromMime($options['mime']);
            }

            $audioContents['content'] = file_get_contents($audio);
            $audioContents['extension'] = $extension;
            $audioContents['mime'] = $this->getMimeFromExtension($extension);
        } else {
            // stream content
            $audioContents['content'] = file_get_contents($audio);
            if (!isset($options['mime'])) {
                PhppptxLogger::logger('Stream content. Set the mime option.', 'fatal');
            }
            $audioContents['extension'] = $this->getExtensionFromMime($options['mime']);
            $audioContents['mime'] = $options['mime'];
        }

        // check if the audio can be obtained
        if (!$audioContents['content']) {
            PhppptxLogger::logger('Unable to get the audio.', 'fatal');
        }

        // check mime type
        if (!in_array($audioContents['mime'], array('audio/mpeg', 'audio/unknown', 'audio/x-ms-wma', 'audio/x-wav'))) {
            PhppptxLogger::logger('Audio format \''.$audioContents['mime'].'\' is not supported.', 'fatal');
        }

        return $audioContents;
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
            case 'audio/mpeg':
                $extension = 'mp3';
                break;
            case 'audio/unknown':
                $extension = 'flac';
                break;
            case 'audio/x-ms-wma':
                $extension = 'wma';
                break;
            case 'audio/x-wav':
                $extension = 'wav';
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
            case 'flac':
                $mime = 'audio/unknown';
                break;
            case 'mp3':
                $mime = 'audio/mpeg';
                break;
            case 'wma':
                $mime = 'audio/x-ms-wma';
                break;
            case 'wav':
                $mime = 'audio/x-wav';
                break;
            default:
                break;
        }

        return strtolower($mime);
    }
}