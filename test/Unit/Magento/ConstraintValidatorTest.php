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

namespace PrOOxxy\MagentoComposerConstraints\Test\Unit\Magento;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Semver\Constraint\ConstraintInterface;
use PHPUnit\Framework\TestCase;
use PrOOxxy\MagentoComposerConstraints\Magento\ConstraintValidator;

class ConstraintValidatorTest extends TestCase
{

    private $configMock;

    private $composerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->configMock = $this->getMockBuilder(\Composer\Config::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock->method('get')->with('platform')->willReturn(null);

        $this->composerMock = $this->getMockBuilder(Composer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getConfig'])
            ->getMock();

        $this->composerMock->method('getConfig')->willReturn($this->configMock);
    }

    /**
     * @test
     */
    public function satisfiesReturnsViolations(): void
    {

        $vendorPackageMock = $this->createMock(PackageInterface::class);
        $vendorPackageMock->method('getName')->willReturn('dep');
        $vendorPackageMock->method('getVersion')->willReturn('1.0');

        $constraint = $this->createMock(ConstraintInterface::class);
        $constraint->method('getPrettyString')->willReturn('>= 2.0');

        $linkMock = $this->getMockBuilder(Link::class)->disableOriginalConstructor()->getMock();
        $linkMock->method('getConstraint')->willReturn($constraint);
        $linkMock->method('getTarget')->willReturn('dep');

        $packageMock = $this->createMock(PackageInterface::class);
        $packageMock->method('getRequires')->willReturn([$linkMock]);
        $packageMock->method('getDevRequires')->willReturn([]);

        $sut = new ConstraintValidator($this->composerMock, [$vendorPackageMock], [$packageMock]);

        $result = $sut->satisfies($packageMock);

        self::assertCount(1, $result);
    }

    /**
     * @test
     */
    public function satisfiesReturnsEmpty(): void
    {
        $vendorPackageMock = $this->createMock(PackageInterface::class);
        $vendorPackageMock->method('getName')->willReturn('dep');
        $vendorPackageMock->method('getVersion')->willReturn('1.0.0');

        $constraint = $this->createMock(ConstraintInterface::class);
        $constraint->method('getPrettyString')->willReturn('= 1.0.0');

        $linkMock = $this->getMockBuilder(Link::class)->disableOriginalConstructor()->getMock();
        $linkMock->method('getConstraint')->willReturn($constraint);
        $linkMock->method('getTarget')->willReturn('dep');

        $packageMock = $this->createMock(PackageInterface::class);
        $packageMock->method('getRequires')->willReturn([$linkMock]);
        $packageMock->method('getDevRequires')->willReturn([]);

        $sut = new ConstraintValidator($this->composerMock, [$vendorPackageMock], [$packageMock]);

        $result = $sut->satisfies($packageMock);

        self::assertEmpty($result);
    }
}
