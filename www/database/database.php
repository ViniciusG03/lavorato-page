<?php
class Database
{
    private $servername;
    private $username;
    private $password;
    private $database;

    public function __construct()
    {
        $this->servername = "mysql.lavoratoguias.kinghost.net"; 
        $this->username = "lavoratoguias";
        $this->password = "A3g7K2m9T5p8L4v6";
        $this->database = "lavoratoguias";
    }

    public function connect()
    {
        try {
            $dsn = "mysql:host=" . $this->servername . ";dbname=" . $this->database;
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexão bem-sucedida";
            return $pdo;
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public function close()
    {
        $this->pdo = null;
    }
}

$database = new Database();
$conn = $database->connect();
?>