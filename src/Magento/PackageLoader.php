<?php
declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Magento;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Package\Loader\ArrayLoader;
use Composer\Package\Loader\JsonLoader;
use UnexpectedValueException;

class PackageLoader implements LoaderInterface
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
        $jsonLoader = new JsonLoader($l);

        $packages = [];
        foreach ($files as $rawFile) {
            $jsonFile = new JsonFile($rawFile);
            try {
                $packages[] = $jsonLoader->load($jsonFile);
            } catch (UnexpectedValueException $e) {
                $this->io->debug($e->getMessage());
            }

        }

        return $packages;
    }

    protected function getGlobPatterns()
    {

        $vendorPath = $this->composer->getConfig()->get('vendor-dir');

        $appCode = implode(DIRECTORY_SEPARATOR, [$vendorPath, '..', 'app', 'code', '*', '*','composer.json']);

        return [
            $appCode
        ];
    }


}
