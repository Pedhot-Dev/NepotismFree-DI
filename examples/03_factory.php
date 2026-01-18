<?php

require __DIR__ . '/../vendor/autoload.php';

use PedhotDev\NepotismFree\Builder\ContainerBuilder;
use PedhotDev\NepotismFree\Contract\ContainerInterface;

class Database
{
    public function __construct(
        public string $host,
        public string $user,
        public string $password
    ) {}
}

class UserRepository
{
    public function __construct(private Database $db) {}
    
    public function find(int $id): string
    {
        return "User {$id} from {$this->db->host}";
    }
}

$builder = new ContainerBuilder();

// Argument binding
$builder->bindArgument(Database::class, 'host', 'localhost');
$builder->bindArgument(Database::class, 'user', 'root');
$builder->bindArgument(Database::class, 'password', 'secret');

// Factory binding
$builder->bind('special_repo', function (ContainerInterface $c) {
    // Manually constructing with values that might come from elsewhere
    $db = new Database('192.168.1.50', 'admin', 'adminpass');
    return new UserRepository($db);
});

$container = $builder->build();

// 1. Standard resolution with arguments
$db = $container->get(Database::class);
echo "Standard DB Host: " . $db->host . PHP_EOL;

// 2. Factory resolution
$repo = $container->get('special_repo');
echo "Factory Repo: " . $repo->find(1) . PHP_EOL;
