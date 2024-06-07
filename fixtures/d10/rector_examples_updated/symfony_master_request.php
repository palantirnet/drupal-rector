<?php

$request = \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST;


class Foo {
    public function bar(\Symfony\Component\HttpKernel\HttpKernelInterface $request) {
        $request = \Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST; // Did it update?
    }
}
