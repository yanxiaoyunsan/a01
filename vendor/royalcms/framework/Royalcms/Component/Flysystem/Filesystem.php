<?php namespace Royalcms\Component\Flysystem;

use InvalidArgumentException;
use Royalcms\Component\Flysystem\Util\ContentListingFormatter;
use BadMethodCallException;
use Royalcms\Component\Flysystem\FilesystemInterface;
use Royalcms\Component\Flysystem\PluginInterface;
use LogicException;
use Royalcms\Component\Flysystem\Plugin\PluginNotFoundException;


/**
 * @method array getWithMetadata(string $path, array $metadata)
 * @method array listFiles(string $path = '', boolean $recursive = false)
 * @method array listPaths(string $path = '', boolean $recursive = false)
 * @method array listWith(array $keys = array(), $directory = '', $recursive = false)
 */
class Filesystem implements FilesystemInterface
{
    
    /**
     * @var Config
     */
    protected $config;
    
    /**
     * Set the config.
     *
     * @param Config|array|null $config
     */
    protected function setConfig($config)
    {
        $this->config = $config ? Util::ensureConfig($config) : null;
    }
    
    /**
     * Get the Config.
     *
     * @return Config config object
     */
    public function getConfig()
    {
        if ($this->config === null) {
            return $this->config = new Config;
        }
    
        return $this->config;
    }
    
    /**
     * Convert a config array to a Config object with the correct fallback.
     *
     * @param array $config
     *
     * @return Config
     */
    protected function prepareConfig(array $config)
    {
        $config = new Config($config);
        $config->setFallback($this->getConfig());
    
        return $config;
    }
    
    /**
     * @var array
     */
    protected $plugins = array();
    
    /**
     * Register a plugin.
     *
     * @param PluginInterface $plugin
     *
     * @return $this
     */
    public function addPlugin(PluginInterface $plugin)
    {
        $this->plugins[$plugin->getMethod()] = $plugin;
    
        return $this;
    }
    
    /**
     * Find a specific plugin.
     *
     * @param string $method
     *
     * @throws LogicException
     *
     * @return PluginInterface $plugin
     */
    protected function findPlugin($method)
    {
        if ( ! isset($this->plugins[$method])) {
            throw new PluginNotFoundException('Plugin not found for method: ' . $method);
        }
    
        if ( ! method_exists($this->plugins[$method], 'handle')) {
            throw new LogicException(get_class($this->plugins[$method]) . ' does not have a handle method.');
        }
    
        return $this->plugins[$method];
    }
    
    /**
     * Invoke a plugin by method name.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    protected function invokePlugin($method, array $arguments, FilesystemInterface $filesystem)
    {
        $plugin = $this->findPlugin($method);
        $plugin->setFilesystem($filesystem);
        $callback = array($plugin, 'handle');
    
        return call_user_func_array($callback, $arguments);
    }
    
    /**
     * Plugins pass-through.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @throws BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        try {
            return $this->invokePlugin($method, $arguments, $this);
        } catch (PluginNotFoundException $e) {
            throw new BadMethodCallException(
                'Call to undefined method '
                . get_class($this)
                . '::' . $method
            );
        }
    }

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter
     * @param Config|array     $config
     */
    public function __construct(AdapterInterface $adapter, $config = null)
    {
        $this->adapter = $adapter;
        $this->setConfig($config);
    }

    /**
     * Get the Adapter.
     *
     * @return AdapterInterface adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @inheritdoc
     */
    public function has($path)
    {
        $path = Util::normalizePath($path);

        return (bool) $this->getAdapter()->has($path);
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, array $config = array())
    {
        $path = Util::normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);

        return (bool) $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, array $config = array())
    {
        if ( ! is_resource($resource)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be a valid resource.');
        }

        $path = Util::normalizePath($path);
        $this->assertAbsent($path);
        $config = $this->prepareConfig($config);

        Util::rewindStream($resource);

        return (bool) $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function put($path, $contents, array $config = array())
    {
        $path = Util::normalizePath($path);
        $config = $this->prepareConfig($config);

        if ($this->has($path)) {
            return (bool) $this->getAdapter()->update($path, $contents, $config);
        }

        return (bool) $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function putStream($path, $resource, array $config = array())
    {
        if ( ! is_resource($resource)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be a valid resource.');
        }

        $path = Util::normalizePath($path);
        $config = $this->prepareConfig($config);
        Util::rewindStream($resource);

        if ($this->has($path)) {
            return (bool) $this->getAdapter()->updateStream($path, $resource, $config);
        }

        return (bool) $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function readAndDelete($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);
        $contents = $this->read($path);

        if ($contents === false) {
            return false;
        }

        $this->delete($path);

        return $contents;
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, array $config = array())
    {
        $path = Util::normalizePath($path);
        $config = $this->prepareConfig($config);

        $this->assertPresent($path);

        return (bool) $this->getAdapter()->update($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, array $config = array())
    {
        if ( ! is_resource($resource)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be a valid resource.');
        }

        $path = Util::normalizePath($path);
        $config = $this->prepareConfig($config);
        $this->assertPresent($path);
        Util::rewindStream($resource);

        return (bool) $this->getAdapter()->updateStream($path, $resource, $config);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if ( ! ($object = $this->getAdapter()->read($path))) {
            return false;
        }

        return $object['contents'];
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if ( ! $object = $this->getAdapter()->readStream($path)) {
            return false;
        }

        return $object['stream'];
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $path = Util::normalizePath($path);
        $newpath = Util::normalizePath($newpath);
        $this->assertPresent($path);
        $this->assertAbsent($newpath);

        return (bool) $this->getAdapter()->rename($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $path = Util::normalizePath($path);
        $newpath = Util::normalizePath($newpath);
        $this->assertPresent($path);
        $this->assertAbsent($newpath);

        return $this->getAdapter()->copy($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->delete($path);
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        $dirname = Util::normalizePath($dirname);

        if ($dirname === '') {
            throw new RootViolationException('Root directories can not be deleted.');
        }

        return (bool) $this->getAdapter()->deleteDir($dirname);
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, array $config = array())
    {
        $dirname = Util::normalizePath($dirname);
        $config = $this->prepareConfig($config);

        return (bool) $this->getAdapter()->createDir($dirname, $config);
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        $directory = Util::normalizePath($directory);
        $contents = $this->getAdapter()->listContents($directory, $recursive);
        $formatter = new ContentListingFormatter($directory, $recursive);
        return $formatter->formatListing($contents);
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if ( ! $object = $this->getAdapter()->getMimetype($path)) {
            return false;
        }

        return $object['mimetype'];
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if ( ! $object = $this->getAdapter()->getTimestamp($path)) {
            return false;
        }

        return $object['timestamp'];
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if (($object = $this->getAdapter()->getVisibility($path)) === false) {
            return false;
        }

        return $object['visibility'];
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        $path = Util::normalizePath($path);

        if (($object = $this->getAdapter()->getSize($path)) === false || ! isset($object['size'])) {
            return false;
        }

        return (int) $object['size'];
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        $path = Util::normalizePath($path);

        return (bool) $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        return $this->getAdapter()->getMetadata($path);
    }

    /**
     * @inheritdoc
     */
    public function get($path, Handler $handler = null)
    {
        $path = Util::normalizePath($path);

        if ( ! $handler) {
            $metadata = $this->getMetadata($path);
            $handler = $metadata['type'] === 'file' ? new File($this, $path) : new Directory($this, $path);
        }

        $handler->setPath($path);
        $handler->setFilesystem($this);

        return $handler;
    }

    /**
     * Assert a file is present.
     *
     * @param string $path path to file
     *
     * @throws FileNotFoundException
     */
    public function assertPresent($path)
    {
        if ( ! $this->has($path)) {
            throw new FileNotFoundException($path);
        }
    }

    /**
     * Assert a file is absent.
     *
     * @param string $path path to file
     *
     * @throws FileExistsException
     */
    public function assertAbsent($path)
    {
        if ($this->has($path)) {
            throw new FileExistsException($path);
        }
    }
}
