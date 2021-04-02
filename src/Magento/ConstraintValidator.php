<?php
declare(strict_types=1);

namespace PrOOxxy\MagentoComposerConstraints\Magento;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\PlatformRepository;
use Composer\Semver\Semver;
use InvalidArgumentException;

class ConstraintValidator
{
    /**
     * @var array
     */
    private $packages;

    /**
     * @var PlatformRepository
     */
    private $platformRepo;

    /**
     * @var PackageInterface[]
     */
    private array $customPackages;

    public function __construct(
        Composer $composer,
        array $vendorPackages,
        array $customPackages
    ) {

        foreach ($vendorPackages as $package) {
            if (!$package instanceof PackageInterface) {
                throw new InvalidArgumentException();
            }
        }

        foreach ($vendorPackages as $vendorPackage) {
            $this->packages[$vendorPackage->getName()] = $vendorPackage;
        }

        $platformOverrides = $composer->getConfig()->get('platform') ?: array();
        $platformRepo = new PlatformRepository(array(), $platformOverrides);

        $this->platformRepo = $platformRepo;
        $this->customPackages = $customPackages;
    }

    private function getPackage(Link $link)
    {

        foreach ($this->customPackages as $package) {
            if ($package->getName() === $link->getTarget()) {
                return $package;
            }
        }

        $platformPackages = $this->platformRepo->getPackages();
        foreach ($platformPackages as $package){
            if ($package->getName() === $link->getTarget()) {
                return $package;
            }
        }

        return $this->packages[$link->getTarget()] ?? null;
    }

    /**
     * Checks if installed packages meet the provided package requirements
     *
     * Returns array of violation messages if there are any.
     * If satisfies - returns empty array
     *
     * @return array
     */
    public function satisfies(PackageInterface $package): array
    {
        $requires = $package->getRequires();
        $requiresDev = $package->getDevRequires();

        return $this->processLinks(array_merge($requires, $requiresDev));
    }

    private function processLinks(array $links): array
    {
        $violations = [];

        foreach ($links as $link) {
            $package = $this->getPackage($link);
            if (($package === null) || Semver::satisfies($package->getVersion(), $link->getConstraint()->getPrettyString())) {
                continue;
            }

            $violations[] = '<error> - ['.$package->getName().'] Currently installed: '.$package->getVersion().' does not satisfy constraint '.$link->getConstraint()->getPrettyString().'</error>';
        }
        return $violations;
    }

}
