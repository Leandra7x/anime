<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

/**
 * Composer manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Composer extends FileContent
{
    /**
     * Add the package into composer requirements
     *
     * @param string $package
     * @param string $version
     */
    public function addPackage($package, $version)
    {
        $config = $this->getContent();
        $config['require'][$package] = $version;
        $this->setContent($config);
    }

    /**
     * Remove the package from composer requirements
     *
     * @param string $package
     */
    public function removePackage($package)
    {
        $config = $this->getContent();
        if (isset($config['require'][$package])) {
            unset($config['require'][$package]);
            $this->setContent($config);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Manipulator\FileContent::getContent()
     */
    protected function getContent()
    {
        return (array)json_decode(parent::getContent(), true);
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Manipulator\FileContent::setContent()
     */
    protected function setContent($content)
    {
        parent::setContent(json_encode($content, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
}