<?php

namespace Iadvize\Profiler\Model;

use Iadvize\Profiler\Model\Profiler;

/**
 * Class ProfilerSession
 *
 * @package Iadvize\Profiler
 */
class ProfilerSession
{
    /** @var Profiler profiling results */
    protected $profiler;

    /** @var string Unique key for profiling session. Will be used as log file name with profiling results */
    protected $key;

    /** @var float Time of profiling session start (including milliseconds) */
    protected $startTime;

    /** @var float Time of lats measurement (including milliseconds) */
    protected $latsStepTime;

    /**  @var int Index number of current profiling step (first is 1) */
    protected $currentStep;

    /**
     * Unique identifier of profiling session. This differs $this->key so that
     * many sessions can be started (multipe http sessions at the same time) but
     * this will be unique for each of them.
     * For generation mechanism  please see {@link http://php.net/manual/ru/function.mt-rand.php}
     *
     * @var int
     */
    protected $id;

    /**
     * @const string Default message to be logged when profilng session will be finished
     */
    const DEFAULT_STOP_MESSAGE = 'Execution is complete. Total time is ';

    /**
     * @const string Default message to be be logged when profiling session starts
     */
    const DEFAULT_START_MESSAGE = 'Execution started';

    /**
     * @param Profiler $profiler Instance of profiler that has this session started.
     * @param string   $key      Unique key for profiling session.
     * @param string   $message  Custom start message. If not specified will be used default one.
     */
    public function __construct(Profiler $profiler, $key, $message = '')
    {
        $this->profiler     = $profiler;
        $this->key          = $key;
        $this->id           = uniqid();
        $this->startTime    = $this->getTime();
        $this->latsStepTime = $this->startTime;
        $this->currentStep  = 1;

        if (empty($message)) {
            $message = self::DEFAULT_START_MESSAGE;
        }

        $this->step($message);
    }

    /**
     * Returns current time stamp including milliseconds.
     *
     * @return float
     */
    protected function getTime()
    {
        return microtime(true);
    }

    /**
     * Writes results of measurement to file.
     *
     * @param string $message Message to log. Will be prepended with timestamp and id of profiling session.
     */
    protected function logMeasurement($message)
    {
        $message = sprintf(
            '%s %s %s',
            $this->getTimeStamp(),
            $this->id,
            $message
        );

        $this->profiler->getLogger()->info($message);
    }

    /**
     * Indicates last step of profiling session. Logs information about total
     * time spent for entire profiling session.
     *
     * @param string $message Custom stop message. If not specified will be used default one.
     */
    public function stop($message = '')
    {
        if (empty($message)) {
            $message = self::DEFAULT_STOP_MESSAGE;
        }

        $this->step($message, true);
    }

    /**
     * Performs new measurement and logs results.
     *
     * @param string $message Description of current measurement step.
     * @param bool   $final   Indicates ending of profiling session.
     *
     * @return void
     */
    public function step($message = '', $final = false)
    {
        if ($this->currentStep == 1) {
            $stepTime = $this->startTime;
        } else {
            if ($final) {
                $stepTime = $this->getFinalTime();
            } else {
                $stepTime = $this->getStepTime();
            }
        }

        $message = sprintf(
            '%s %s %s sec',
            $this->currentStep,
            $message,
            $stepTime
        );

        $this->currentStep++;

        $this->logMeasurement($message);
    }

    /**
     * Returns total time spent for entire profiling session.
     *
     * @return float
     */
    protected function getFinalTime()
    {
        $currentTime = $this->getTime();
        $result      = $currentTime - $this->startTime;

        return $result;
    }

    /**
     * Returns time spent since last step.
     *
     * @return float
     */
    protected function getStepTime()
    {
        $currentTime        = $this->getTime();
        $result             = $currentTime - $this->latsStepTime;
        $this->latsStepTime = $currentTime;

        return $result;
    }

    /**
     * Returns timestamp for logging.
     *
     * @return string
     */
    protected function getTimeStamp()
    {
        return time() . ' (' . strftime('%Y.%m.%d %H:%I:%S', time()) . ')';
    }
}
