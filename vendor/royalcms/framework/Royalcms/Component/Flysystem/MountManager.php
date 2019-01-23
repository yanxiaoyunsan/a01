<?php namespace Royalcms\Component\Flysystem;

use InvalidArgumentException;
use Royalcms\Component\Flysystem\Plugin\PluginNotFoundException;
use LogicException;
use BadMethodCallException;
use Royalcms\Component\Flysystem\FilesystemInterface;
use Royalcms\Component\Flysystem\PluginInterface;

/**
 * Class MountManager.
 *
 * Proxies methods to Filesystem (@see __call):
 *
 * @method AdapterInterface getAdapter($prefix)
 * @method Config getConfig($prefix)
 * @method bool has($path)
 * @method bool write($path, $contents, array $config = array())
 * @method bool writeStream($path, $resource, array $config = array())
 * @method bool put($path, $contents, $config = array())
 * @method bool putStream($path, $contents, $config = array())
 * @method string readAndDelete($path)
 * @method bool update($path, $contents, $config = array())
 * @method bool updateStream($path, $resource, $config = array())
 * @method string|false read($path)
 * @method resource|false readStream($path)
 * @method bool rename($path, $newpath)
 * @method bool delete($path)
 * @method bool deleteDir($dirname)
 * @method bool createDir($dirname, $config = array())
 * @method array listFiles($directory = '', $recursive = false)
 * @method array listPaths($directory = '', $recursive = false)
 * @method array getWithMetadata($path, array $metadata)
 * @method string|false getMimetype($path)
 * @method string|false getTimestamp($path)
 * @method string|false getVisibility($path)
 * @method int|false getSize($path);
 * @method bool setVisibility($path, $visibility)
 * @method array|false getMetadata($path)
 * @method Handler get($path, Handler $handler = null)
 * @method Filesystem flushCache()
 * @method assertPresent($path)
 * @method assertAbsent($path)
 */
class MountManager
{
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
     * @var array
     */
    protected $filesystems = array();

    /**
     * Constructor.
     *
     * @param array $filesystems
     */
    public function __construct(array $filesystems = array())
    {
        $this->mountFilesystems($filesystems);
    }

    /**
     * Mount filesystems.
     *
     * @param array $filesystems [:prefix => Filesystem,]
     *
     * @return $this
     */
    public function mountFilesystems(array $filesystems)
    {
        foreach ($filesystems as $prefix => $filesystem) {
            $this->mountFilesystem($prefix, $filesystem);
        }

        return $this;
    }

    /**
     * Mount filesystems.
     *
     * @param string              $prefix
     * @param FilesystemInterface $filesystem
     *
     * @return $this
     */
    public function mountFilesystem($prefix, FilesystemInterface $filesystem)
    {
        if ( ! is_string($prefix)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #1 to be a string.');
        }

        $this->filesystems[$prefix] = $filesystem;

        return $this;
    }

    /**
     * Get the filesystem with the corresponding prefix.
     *
     * @param string $prefix
     *
     * @throws LogicException
     *
     * @return FilesystemInterface
     */
    public function getFilesystem($prefix)
    {
        if ( ! isset($this->filesystems[$prefix])) {
            throw new LogicException('No filesystem mounted with prefix ' . $prefix);
        }

        return $this->filesystems[$prefix];
    }

    /**
     * Retrieve the prefix from an arguments array.
     *
     * @param array $arguments
     *
     * @return array [:prefix, :arguments]
     */
    public function filterPrefix(array $arguments)
    {
        if (empty($arguments)) {
            throw new LogicException('At least one argument needed');
        }

        $path = array_shift($arguments);

        if ( ! is_string($path)) {
            throw new InvalidArgumentException('First argument should be a string');
        }

        if ( ! preg_match('#^.+\:\/\/.*#', $path)) {
            throw new InvalidArgumentException('No prefix detected in path: ' . $path);
        }

        list($prefix, $path) = explode('://', $path, 2);
        array_unshift($arguments, $path);

        return array($prefix, $arguments);
    }

    /**
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        list($prefix, $arguments) = $this->filterPrefix(array($directory));
        $filesystem = $this->getFilesystem($prefix);
        $directory = array_shift($arguments);
        $result = $filesystem->listContents($directory, $recursive);

        foreach ($result as &$file) {
            $file['filesystem'] = $prefix;
        }

        return $result;
    }

    /**
     * Call forwarder.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        list($prefix, $arguments) = $this->filterPrefix($arguments);

        return $this->invokePluginOnFilesystem($method, $arguments, $prefix);
    }

    /**
     * @param $from
     * @param $to
     *
     * @return bool
     */
    public function copy($from, $to)
    {
        list($prefixFrom, $arguments) = $this->filterPrefix(array($from));

        $fsFrom = $this->getFilesystem($prefixFrom);
        $buffer = call_user_func_array(array($fsFrom, 'readStream'), $arguments);

        if ($buffer === false) {
            return false;
        }

        list($prefixTo, $arguments) = $this->filterPrefix(array($to));

        $fsTo = $this->getFilesystem($prefixTo);
        $result =  call_user_func_array(array($fsTo, 'writeStream'), array_merge($arguments, array($buffer)));

        if (is_resource($buffer)) {
            fclose($buffer);
        }

        return $result;
    }

    /**
     * List with plugin adapter.
     *
     * @param array  $keys
     * @param string $directory
     * @param bool   $recursive
     */
    public function listWith(array $keys = array(), $directory = '', $recursive = false)
    {
        list($prefix, $arguments) = $this->filterPrefix(array($directory));
        $directory = $arguments[0];
        $arguments = array($keys, $directory, $recursive);

        return $this->invokePluginOnFilesystem('listWith', $arguments, $prefix);
    }

    /**
     * Move a file.
     *
     * @param $from
     * @param $to
     *
     * @return bool
     */
    public function move($from, $to)
    {
        $copied = $this->copy($from, $to);

        if ($copied) {
            return $this->delete($from);
        }

        return false;
    }

    /**
     * Invoke a plugin on a filesystem mounted on a given prefix.
     *
     * @param $method
     * @param $arguments
     * @param $prefix
     *
     * @return mixed
     */
    public function invokePluginOnFilesystem($method, $arguments, $prefix)
    {
        $filesystem = $this->getFilesystem($prefix);

        try {
            return $this->invokePlugin($method, $arguments, $filesystem);
        } catch (PluginNotFoundException $e) {
            // Let it pass, it's ok, don't panic.
        }

        $callback = array($filesystem, $method);

        return call_user_func_array($callback, $arguments);
    }
}
