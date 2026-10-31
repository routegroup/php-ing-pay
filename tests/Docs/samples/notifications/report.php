<?php

declare(strict_types=1);

/** @var string $body */

// #region sample
use Routegroup\IngPay\Notification\ReportNotification;

$report = new ReportNotification(json_decode($body, true, flags: JSON_THROW_ON_ERROR));

// $report->url is where the .{$report->fileExt} file can be downloaded
// #endregion sample

return $report;
