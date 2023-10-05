<?php


require_once(__DIR__ . "/../../node/src/Node/Node.php");
require_once(__DIR__ . "/../../vendor/autoload.php");
use Snail\Node\Node;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    private $basePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basePath = sys_get_temp_dir() . '/node_test';
        mkdir($this->basePath . "/config", 0777, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->deleteDirectory($this->basePath);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        $iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }

    public function testCreate()
    {
        $GLOBALS["base_path"] = $this->basePath;
        Node::create();
        $this->assertFileExists($this->basePath . "/config/" . Node::NODE_FILE);
        $this->assertFileExists($this->basePath . "/config/" . Node::ROUTING_FILE);
        $this->assertFileExists($this->basePath . "/config/" . Node::CONNECTED_FILE);
        $this->assertFileExists($this->basePath . "/config/" . Node::LOG_FILE);
    }

    public function testRead()
    {
        $GLOBALS["base_path"] = $this->basePath;
        $node = new Node();
        $nodeData = [
            'id' => 'test_id',
            'type' => 'individual',
            'sync_rate' => 24,
            'sync_unit' => 'hour',
            'created_at' => time(),
            'disconnect_time' => (24 * 7),
            'last_sync' => "",
        ];
        file_put_contents($this->basePath . "/config/" . Node::NODE_FILE, json_encode($nodeData));
        $node->read();
        $this->assertEquals('test_id', $node->getId());
    }
}
