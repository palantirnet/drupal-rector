<?php

use Symfony\Component\HttpKernel\HttpKernelInterface;
$request = HttpKernelInterface::MAIN_REQUEST;

class Foo {
    public function bar(HttpKernelInterface $request) {
        $request = HttpKernelInterface::MAIN_REQUEST; // Did it update?
    }
}
