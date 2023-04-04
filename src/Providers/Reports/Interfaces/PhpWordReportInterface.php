<?php

namespace sergeynilov\QuizzesInit\Providers\Reports\Interfaces;

interface PhpWordReportInterface
{
    public function generate(): self;

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse;
}

