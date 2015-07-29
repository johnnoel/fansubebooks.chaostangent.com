<?php
/**
 * Convert MySQL fansubebooks to Postgres
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

$mysqlConfig = [
    'host' => '127.0.0.1',
    'user' => 'fansubebooks',
    'password' => '',
    'db' => 'fansubebooks',
];

$postgresConfig = [
    'host' => '127.0.0.1',
    'user' => 'fansubebooks',
    'password' => '',
    'db' => 'fansubebooks',
];

$mysql = null;
$postgres = null;

try {
    $mysql = new PDO(sprintf('mysql:host=%s;dbname=%s', $mysqlConfig['host'], $mysqlConfig['db']), $mysqlConfig['user'], $mysqlConfig['password'], [
        PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8',
    ]);

    $postgres = new PDO(sprintf('pgsql:host=%s;dbname=%s', $postgresConfig['host'], $postgresConfig['db']), $postgresConfig['user'], $postgresConfig['password']);
    $postgres->query("SET NAMES 'UTF8'");
} catch (\PDOException $e) {
    var_dump($e);
    exit('Unable to connect to one of the databases'.PHP_EOL);
}

/**
 * Helper function for converting a string Europe/London date into a string UTC
 * date
 *
 * @param string $stringDate In the format "Y-m-d H:i:s"
 * @return string A UTC formatted date "Y-m-d H:i:s"
 */
function convertDateTimeToUTC($stringDate) {
    $obj = \DateTime::createFromFormat('Y-m-d H:i:s', $stringDate, new \DateTimeZone('Europe/London'));
    $obj->setTimezone(new \DateTimeZone('UTC'));

    return $obj->format('Y-m-d H:i:s');
}

// series
$seriesIdMap = [];

$sql = 'SELECT * FROM series ORDER BY title';
$stmt = $mysql->prepare($sql);
$stmt->execute();
$series = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'INSERT INTO series (title, alias, image, thumbnail, added) VALUES (:title, :alias, :image, :thumbnail, :added)';
$stmt = $postgres->prepare($sql);
$postgres->beginTransaction();

foreach ($series as $s) {
    $good = $stmt->execute([
        ':title' => $s['title'],
        ':alias' => $s['alias'],
        ':image' => $s['image'],
        ':thumbnail' => $s['thumbnail'],
        ':added' => convertDateTimeToUTC($s['added']),
    ]);

    if (!$good) {
        $postgres->rollBack();
        exit('Unable to run series query: '.$stmt->errorInfo()[2].PHP_EOL);
    }

    $seriesIdMap[$s['id']] = $postgres->lastInsertId('series_id_seq');
}

$postgres->commit();

// files
$fileIdMap = [];

$sql = 'SELECT * FROM files ORDER BY name';
$stmt = $mysql->prepare($sql);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'INSERT INTO files (series_id, name, hash, added) VALUES (:series_id, :name, :hash, :added)';
$stmt = $postgres->prepare($sql);
$postgres->beginTransaction();

foreach ($files as $file) {
    $good = $stmt->execute([
        ':series_id' => $seriesIdMap[$file['series_id']],
        ':name' => $file['name'],
        ':hash' => $file['hash'],
        ':added' => convertDateTimeToUTC($file['added']),
    ]);

    if (!$good) {
        $postgres->rollBack();
        exit('Unable to run files query: '.$stmt->errorInfo()[2].PHP_EOL);
    }

    $fileIdMap[$file['id']] = $postgres->lastInsertId('files_id_seq');
}

$postgres->commit();

// lines
$lineIdMap = [];

$sql = 'SELECT * FROM `lines` ORDER BY file_id';
$stmt = $mysql->prepare($sql);
$stmt->execute();
$lines = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'INSERT INTO lines (file_id, line, charactercount) VALUES (:file_id, :line, :cc)';
$stmt = $postgres->prepare($sql);
$postgres->beginTransaction();

foreach ($lines as $line) {
    $data = [
        ':file_id' => $fileIdMap[$line['file_id']],
        ':line' => utf8_encode($line['line']),
        ':cc' => $line['charactercount'],
    ];
    $good = $stmt->execute($data);

    if (!$good) {
        $postgres->rollBack();
        var_dump($data);
        exit('Unable to run lines query: '.$stmt->errorInfo()[2].PHP_EOL);
    }

    $lineIdMap[$line['id']] = $postgres->lastInsertId('lines_id_seq');
}

$postgres->commit();

unset($lines);

// votes
$sql = 'SELECT * FROM votes ORDER BY added';
$stmt = $mysql->prepare($sql);
$stmt->execute();
$votes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'INSERT INTO votes (line_id, added, ip, positive) VALUES (:line_id, :added, :ip, :positive)';
$voteStmt = $postgres->prepare($sql);

$sql = 'INSERT INTO flags (line_id, added, ip) VALUES (:line_id, :added, :ip)';
$flagStmt = $postgres->prepare($sql);

$postgres->beginTransaction();

foreach ($votes as $vote) {
    $good = false;

    if ($vote['polarity'] == 0) {
        $good = $flagStmt->execute([
            ':line_id' => $lineIdMap[$vote['line_id']],
            ':added' => convertDateTimeToUTC($vote['added']),
            ':ip' => $vote['ip'],
        ]);

        if (!$good) {
            $postgres->rollBack();
            exit('Unable to run query: '.$flagStmt->errorInfo()[2].PHP_EOL);
        }
    } else {
        $good = $voteStmt->execute([
            ':line_id' => $lineIdMap[$vote['line_id']],
            ':added' => $vote['added'],
            ':ip' => $vote['ip'],
            ':positive' => ($vote['polarity'] > 0) ? 1 : 0,
        ]);

        if (!$good) {
            $postgres->rollBack();
            exit('Unable to run query: '.$voteStmt->errorInfo()[2].PHP_EOL);
        }
    }
}

$postgres->commit();

// tweets
$sql = 'SELECT * FROM tweets ORDER BY tweeted';
$stmt = $mysql->prepare($sql);
$stmt->execute();
$tweets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = 'INSERT INTO tweets (line_id, tweeted) VALUES (:line_id, :tweeted)';
$stmt = $postgres->prepare($sql);
$postgres->beginTransaction();

foreach ($tweets as $tweet) {
    $good = $stmt->execute([
        ':line_id' => $lineIdMap[$tweet['line_id']],
        ':tweeted' => convertDateTimeToUTC($tweet['tweeted']),
    ]);

    if (!$good) {
        $postgres->rollBack();
        exit('Unable to run query: '.$stmt->errorInto()[2].PHP_EOL);
    }
}

$postgres->commit();
