<?php
/**
 * Copyright © Hampus Westman 2021
 * See LICENCE provided with this module for licence details
 *
 * @author     Hampus Westman <hampus.westman@gmail.com>
 * @copyright  Copyright (c) 2021 Hampus Westman
 * @license    MIT License https://opensource.org/licenses/MIT
 * @link       https://github.com/Pr00xxy
 */

declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Magento;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Loader\JsonLoader;
use UnexpectedValueException;

class PackageLoader
{

    /**
     * @var Composer
     */
    private $composer;

    /**
     * @var IOInterface
     */
    private $io;

    public function __construct(
        Composer $composer,
        IOInterface $Io
    ) {
        $this->composer = $composer;
        $this->io = $Io;
    }

    public function load(): array
    {
        $files = [];

        $patterns = $this->getGlobPatterns();

        foreach ($patterns as $pattern) {
            foreach (glob($pattern) as $match) {
                $files[] = $match;
            }
        }

        $l = new ArrayLoader();
        $json = new JsonLoader($l);

        $packages = [];
        foreach ($files as $rawFile) {
            $jsonFile = new JsonFile($rawFile);
            try {
                $packages[] = $json->load($jsonFile);
            } catch (UnexpectedValueException $e) {
                $this->io->debug($e->getMessage());
            }

        }

        return $packages;
    }

    protected function getGlobPatterns(): array
    {

        $vendorPath = $this->composer->getConfig()->get('vendor-dir');

        if ($vendorPath === null) {
            return [];
        }

        $appCode = implode(DIRECTORY_SEPARATOR, [$vendorPath, '..', 'app', 'code', '*', '*','composer.json']);

        return [
            $appCode
        ];
    }


}
