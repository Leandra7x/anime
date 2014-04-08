<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Symfony\Component\Yaml\Yaml;

/**
 * Migrate
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Migrate
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Migrate extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INIT;

    /**
     * Get path to migrations config file from package
     *
     * @return string|null
     */
    protected function getMigrationsConfig()
    {
        $options = $this->getContainer()->getPackageOptions($this->getPackage());
        // specific location
        if ($options['anime-db-migrations']) {
            return $options['anime-db-migrations'];
        }

        $dir = __DIR__.'/../../../../../../../vendor/'.$this->getPackage()->getName().'/';
        if (file_exists($dir.'migrations.yml')) {
            return $dir.'migrations.yml';
        } elseif (file_exists($dir.'migrations.xml')) {
            return $dir.'migrations.xml';
        }

        return null;
    }

    /**
     * Get migrations namespace and directory
     *
     * @param string $file
     *
     * @return array {namespace:string, directory:string}
     */
    protected function getNamespaceAndDirectory($file)
    {
        $namespace = '';
        $directory = '';

        $config = file_get_contents($file);
        switch (pathinfo($file, PATHINFO_EXTENSION)) {
            case 'yml':
                $config = Yaml::parse($config);
                if (isset($config['migrations_namespace'])) {
                    $namespace = $config['migrations_namespace'];
                }
                if (isset($config['migrations_directory'])) {
                    $directory = $config['migrations_directory'];
                }
                break;
            case 'xml':
                $doc = new \DOMDocument();
                $doc->loadXML($config);
                $xpath = new \DOMXPath($doc);
                $list = $xpath->query('/doctrine-migrations/migrations-namespace');
                if ($list->length) {
                    $namespace = $list->item(0)->nodeValue;
                }
                $list = $xpath->query('/doctrine-migrations/migrations-directory');
                if ($list->length) {
                    $directory = $list->item(0)->nodeValue;
                }
                break;
        }

        return [
            'namespace' => $namespace && $namespace[0] == '\\' ? substr($namespace, 1) : $namespace,
            'directory' => $directory
        ];
    }
}