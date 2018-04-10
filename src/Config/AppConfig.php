<?php

namespace App\Config;

class AppConfig 
{
    /*
     * @var array
     */
    protected $config;

    public function __construct(array $config = []) {
        $this->config = $config;
    }

    public function getLocalDir() {
        return $this->config['local_dir'] . '/' . $this->getDatabase();
    }

    public function getDatabase() {
        $connection = $this->getConnection();
        $result = $connection->getDatabase();

        if (!$result) {
            throw new \RuntimeException("Database parameter is required");
        }

        return $result;
    }

    /**
     * return \Doctrine\DBAL\Connection
     */
    public function getConnection() {
        $url = $this->config['database_url'];
        $connection = \Doctrine\DBAL\DriverManager::getConnection(['url' => $url]);

        return $connection;
    }

    public function getConnectionParams() {
        $result = [];

        $connection = $this->getConnection();

        if ($host = $connection->getHost()) {
            $result['host'] = sprintf('--host=%s', $host);
        }

        if ($password = $connection->getPassword()) {
            $result['password'] = sprintf('--password=%s', $password);
        }

        if ($port = $connection->getPort()) {
            $result['port'] = sprintf('--port=%s', $port);
        }

        if ($username = $connection->getUsername()) {
            $result['user'] = sprintf('--user=%s', $username);
        }

        $database = $connection->getDatabase();
        $result['database'] = sprintf('--database=%s', $database);

        return $result;
    }
}