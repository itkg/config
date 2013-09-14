<?php
use itkg\Exception\NotFoundException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Class Lemon
 *
 * @author Pascal DENIS <pascal.denis.75@gmail.com>
 */
class Lemon
{
    /**
     * Runtime config for service 
     * 
     * @static
     * @var array
     */
    public static $config;

    /**
     * Debug mode 
     * 
     * @var boolean
     */
    protected $isDebug;
  
    /**
     * Extension's list
     * 
     * @var array
     */
    protected $extensions;

    /**
     * Cache file path
     * 
     * @var string
     */
    protected $cacheFile;

    /**
     * DIC
     * 
     * @var ContainerBuilder
     */
    protected static $container;

    /**
     * Constructor
     * 
     * @param string  $cacheFile Path to cache file
     * @param boolean $isDebug   Debug mode
     */
    public function __construct($cacheFile, $isDebug = false)
    {
        $this->isDebug = $isDebug;
        $this->cacheFile = $cacheFile;
    }
    
    /**
     * Load container if it is not loaded
     */
    public function load()
    {
        if (!self::$container) {
            $containerConfigCache = new ConfigCache(
                $this->cacheFile, 
                $this->isDebug
            );
        
            if (!$containerConfigCache->isFresh()) {
                self::$container = new ContainerBuilder();

                foreach ($this->getExtensions() as $extension) {
                    self::$container->registerExtension($extension);
                    self::$container->loadFromExtension($extension->getAlias());  
                }

                self::$container->compile();

                $dumper = new PhpDumper(self::$container);
                $containerConfigCache->write(
                    $dumper->dump(array('class' => 'LemonContainer')),
                    self::$container->getResources()
                );
                return;
            }
            include_once $this->cacheFile;
            self::$container = new LemonContainer();
        }
    }
    
    /**
     * Register an extension
     * 
     * @param ExtensionInterface $extension Extension to register
     */
    public function registerExtension(ExtensionInterface $extension)
    {
        if (!$this->extensions) {
            $this->extensions = array();
        }
        
        $this->extensions[] = $extension;
    }
   
    /**
     * Get extension's list
     * 
     * @return array Extension's list
     */
    public function getExtensions()
    {
        if (!$this->extensions) {
            $this->extensions = array();
        }
        
        return $this->extensions;
    }
    
    /**
     * Set extension's list
     * 
     * @param array $extensions Extension's list
     */
    public function setExtensions(array $extensions = array())
    {
        $this->extensions = $extensions;
    }

    /**
     * Get container
     * 
     * @return ContainerBuilder Container
     */
    public function getContainer()
    {
        return self::$container;
    }
    
    /**
     * Set container
     * 
     * @param ContainerBuilder $container [description]
     */
    public function setContainer(ContainerBuilder $container = null)
    {
        self::$container = $container;
    }

    /**
     * Get service from container By Key
     * Load config if service implements \Lemon\ConfigInterface
     *
     * @throws \Lemon\Exception\NotFoundException
     * 
     * @param string $key Service ID
     * 
     * @return mixed  Service
     */
    public static function get($key)
    {
        if (self::$container->has($key)) {
            $service = self::$container->get($key);
            // If service implements ConfigInterface 
            // && config exists for this service, merge params
            if ($service instanceof \Lemon\ConfigInterface 
                && isset(self::$config[$key])) {
                
                $service->mergeParams(self::$config[$key]);
            }
            return $service;
        }
        throw new NotFoundException(sprintf('Key %s not found', $key));
    }

    /**
     * Check if service exists for param key
     * 
     * @param string  $key Service ID
     * 
     * @return boolean      Service exists or not
     */
    public static function has($key) 
    {
        return self::$container->has($key); 
    }

    /**
     * Display container with print_r
     */
    public static function debug()
    {
        echo '<pre>';
        print_r(self::$container);
        echo '</pre>';
    }

    /**
     * Display config with print_r
     */
    public static function debugConfig()
    {
        echo '<pre>';
        print_r(self::$config);
        echo '</pre>';
    }
}
