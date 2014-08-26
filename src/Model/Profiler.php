<?php

namespace Iadvize\Model\Profiler\Model;

use Iadvize\Profiler\Model\ProfilerSession;

/**
 * Class Profiler
 *
 * @package Iadvize\Profiler
 */
class Profiler
{
    /** @var  Logger logger instance */
    protected $logger;

    /** @var ProfilerSession[] Set of profilingSessions that were started. Contains instances of type ProfilerSession */
    protected $profilerSessions = [];

    /**
     * @param Logger $logger Logger instance
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get property logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Starts profiling session.
     *
     * @param string $key     Unique key for profiling session.
     * @param string $message Custom start message. If not specified will be used default one.
     *
     * @return ProfilerSession Instance of measurer to operate with.
     */
    public function start($key, $message = '')
    {
        $this->profilerSessions[$key] = new ProfilerSession($this, $key, $message);

        return $this->profilerSessions[$key];
    }

    /**
     * Decorator for the ProfilerSession::step() method.
     *
     * @param string $key     Unique key for profiling session.
     * @param string $message Message to describe current step.
     */
    public function step($key, $message = '')
    {
        if (isset($this->profilerSessions[$key])) {
            $this->profilerSessions[$key]->step($message);
        }
    }

    /**
     * Decorator for the ProfilerSession::stop() method.
     *
     * @param string $key     Unique key for profiling session.
     * @param string $message Custom stop message. If not specified will be used default one.
     */
    public function stop($key, $message = '')
    {
        if (isset($this->profilerSessions[$key])) {
            $this->profilerSessions[$key]->stop($message);
        }
    }
}
