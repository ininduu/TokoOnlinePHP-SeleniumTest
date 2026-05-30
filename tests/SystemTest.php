<?php

use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverBy;

class SystemTest extends TestCase
{
    private $driver;
    private $baseUrl = 'http://localhost:8000';

    protected function setUp(): void
    {
        $host = 'http://localhost:9515';

        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments([
            '--headless',
            '--disable-gpu',
            '--no-sandbox'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(
            ChromeOptions::CAPABILITY,
            $chromeOptions
        );

        $this->driver = RemoteWebDriver::create(
            $host,
            $capabilities
        );
    }

    public function testHomepageAndSearchFeature()
    {
        // Buka halaman utama
        $this->driver->get($this->baseUrl);

        // Validasi halaman memuat
        $bodyText = $this->driver
            ->findElement(WebDriverBy::tagName('body'))
            ->getText();

        $this->assertStringContainsString(
            'Toko Online',
            $bodyText
        );

        // Cari produk
        $searchBox = $this->driver->findElement(
            WebDriverBy::name('cari')
        );

        $searchBox->sendKeys('Adidas');
        $searchBox->submit();

        // Validasi hasil pencarian
        $updatedBodyText = $this->driver
            ->findElement(WebDriverBy::tagName('body'))
            ->getText();

        $this->assertStringContainsString(
            'Adidas Adizero V5',
            $updatedBodyText
        );
    }

    protected function tearDown(): void
    {
        if ($this->driver) {
            $this->driver->quit();
        }
    }
}
