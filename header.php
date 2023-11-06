<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titlePage ?></title>
    <link rel="stylesheet" href="styles.css">
    <?php 
    try{
        $pdo = new \PDO('mysql:host=localhost;dbname=project_library', 'root', '');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('SET NAMES "utf8"');
    } catch (\PDOException $e) {
        echo 'Unable to connect to the database server: ' . $e->getMessage();
    }
    ?>
</head>
<body>
    
