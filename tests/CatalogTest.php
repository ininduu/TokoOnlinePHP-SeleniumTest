<?php

use PHPUnit\Framework\TestCase;
use App\Catalog;

class CatalogTest extends TestCase
{
    private $katalog;
    private $testFile = __DIR__ . '/test_products.json';

    protected function setUp(): void
    {
        $dummyData = ["PRD-1" => ["nama" => "Kemeja Flanel", "harga" => 150000, "stok" => 10]];
        file_put_contents($this->testFile, json_encode($dummyData));
        $this->katalog = new Catalog($this->testFile);
    }

    public function testSearchProductFound()
    {
        $result = $this->katalog->searchProduct("Kemeja");
        $this->assertCount(99, $result);
    }

    protected function tearDown(): void
    {
        unlink($this->testFile);
    }
}
