<?php namespace Royalcms\Component\Flysystem\Adapter;

use Royalcms\Component\Flysystem\Config;
use Royalcms\Component\Flysystem\Util;

class NullAdapter extends AbstractAdapter
{
    
    /**
     * Get the contents of a file in a stream.
     *
     * @param string $path
     *
     * @return resource|false false when not found, or a resource
     */
    public function readStream($path)
    {
        if ( ! $data = $this->read($path)) {
            return false;
        }
    
        $stream = tmpfile();
        fwrite($stream, $data['contents']);
        rewind($stream);
        $data['stream'] = $stream;
        unset($data['contents']);
    
        return $data;
    }
    
    /**
     * Stream fallback delegator.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     * @param string   $fallback
     *
     * @return mixed fallback result
     */
    protected function stream($path, $resource, Config $config, $fallback)
    {
        Util::rewindStream($resource);
        $contents = stream_get_contents($resource);
        $fallbackCall = array($this, $fallback);
    
        return call_user_func($fallbackCall, $path, $contents, $config);
    }
    
    /**
     * Write using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config
     *
     * @return mixed false or file metadata
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->stream($path, $resource, $config, 'write');
    }
    
    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object or visibility setting
     *
     * @return mixed false of file metadata
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->stream($path, $resource, $config, 'update');
    }
    
    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $response = $this->readStream($path);

        if ($response === false || ! is_resource($response['stream'])) {
            return false;
        }

        $result = $this->writeStream($newpath, $response['stream'], new Config());

        if ($result !== false && is_resource($response['stream'])) {
            fclose($response['stream']);
        }

        return $result !== false;
    }

    /**
     * Check whether a file is present.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, Config $config)
    {
        $type = 'file';
        $result = compact('contents', 'type', 'size', 'path');
        $visibility = $config->get('visibility');
        if ($visibility) {
            $result['visibility'] = $visibility;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, Config $config)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        return array();
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        return compact('visibility');
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, Config $config)
    {
        return array('path' => $dirname, 'type' => 'dir');
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        return false;
    }
}
