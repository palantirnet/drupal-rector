<?php

// Untyped native PDOStatement — PHPStan cannot confirm StatementInterface, rector skips.
$pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
