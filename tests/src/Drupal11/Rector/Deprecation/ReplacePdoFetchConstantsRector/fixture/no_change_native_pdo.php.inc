<?php

// Receiver of unknown type named like PDO: the type fallback skips it.
$pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
