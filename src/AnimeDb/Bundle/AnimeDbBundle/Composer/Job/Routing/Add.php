<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Routing as BaseRouting;

/**
 * Job: Add package to routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Add extends BaseRouting
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        // TODO get data
//         $this->manipulator->addResource($bundle, $format);
    }
}