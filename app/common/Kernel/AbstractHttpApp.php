<?php
declare(strict_types=1);

namespace App\Common\Kernel;

use App\Common\Kernel;
use Comely\Http\Router;
use Comely\Knit\Knit;
use Comely\Sessions\Sessions;
use Comely\Sessions\Storage\SessionDirectory;

/**
 * Class AbstractHttpApp
 * @package App\Common\Kernel
 */
abstract class AbstractHttpApp extends Kernel
{
    /** @var Kernel\Http */
    private Kernel\Http $http;
    /** @var Sessions */
    private Sessions $sess;
    /** @var Knit */
    private Knit $knit;

    /**
     * AbstractHttpApp constructor.
     * @throws \App\Common\Exception\AppConfigException
     * @throws \App\Common\Exception\AppDirException
     * @throws \Comely\Filesystem\Exception\PathException
     * @throws \Comely\Filesystem\Exception\PathNotExistException
     * @throws \Comely\Filesystem\Exception\PathOpException
     * @throws \Comely\Sessions\Exception\StorageException
     * @throws \Comely\Yaml\Exception\ParserException
     */
    protected function __construct()
    {
        parent::__construct();
        $this->http = new Kernel\Http($this);

        // Sessions
        $this->sess = new Sessions(new SessionDirectory($this->dirs()->sessions()));

        // Knit
        $this->knit = new Knit();
        $this->knit->dirs()->compiler($this->dirs()->knit())
            ->cache($this->dirs()->knit()->dir("cache"));

        $this->knit->modifiers()->registerDefaultModifiers();
    }

    /**
     * @return Router
     */
    public function router(): Router
    {
        return $this->http->router();
    }

    /**
     * @return Kernel\Http
     */
    public function http(): Kernel\Http
    {
        return $this->http;
    }

    /**
     * @return Sessions
     */
    public function sessions(): Sessions
    {
        return $this->sess;
    }

    /**
     * @return Knit
     */
    public function knit(): Knit
    {
        return $this->knit;
    }
}
