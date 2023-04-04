<?php

namespace sergeynilov\QuizzesInit\Library\Facades;

use Arr;
use Carbon\Carbon;
use Illuminate\Support\Str;
use hisorange\BrowserDetect\Parser;

class QuizzesInitFacade
{
    protected static $dateNumbersFormat = 'Y-m-d';
    protected static $dateAstextFormat = 'j F, Y';

    /**
     * Returns the amount of memory, in bytes, that's currently being allocated by this PHP script.

     * @param bool $formatted - if return value must be formatted
     */
    public static function getUsedMemory(bool $formatted = false): string {
        $memory = memory_get_usage(true);
        if($formatted) {
            $unit=array('b','kb','mb','gb','tb','pb');
            return @round($memory/pow(1024,($i=floor(log($memory,1024)))),2).' '.$unit[$i];

        }
        return (string)$memory;
    }

    /**
     * Returns the amount of memory, in bytes, that's currently being allocated by this PHP script.

     * // https://www.php.net/manual/en/function.memory-get-usage.php
     */
    // string
    public static function getServerMemoryUsage() : string
    {
        $memoryTotal = null;
        $memoryFree = null;

        if (stristr(PHP_OS, "win")) {
            // Get total physical memory (this is in bytes)
            $cmd = "wmic ComputerSystem get TotalPhysicalMemory";
            @exec($cmd, $outputTotalPhysicalMemory);

            // Get free physical memory (this is in kibibytes!)
            $cmd = "wmic OS get FreePhysicalMemory";
            @exec($cmd, $outputFreePhysicalMemory);

            if ($outputTotalPhysicalMemory && $outputFreePhysicalMemory) {
                // Find total value
                foreach ($outputTotalPhysicalMemory as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $memoryTotal = $line;
                        break;
                    }
                }

                // Find free value
                foreach ($outputFreePhysicalMemory as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $memoryFree = $line;
                        $memoryFree *= 1024;  // convert from kibibytes to bytes
                        break;
                    }
                }
            }
        }
        else
        {
            if (is_readable("/proc/meminfo"))
            {
                $stats = @file_get_contents("/proc/meminfo");

                if ($stats !== false) {
                    // Separate lines
                    $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
                    $stats = explode("\n", $stats);

                    // Separate values and find correct lines for total and free mem
                    foreach ($stats as $statLine) {
                        $statLineData = explode(":", trim($statLine));

                        // Total memory
                        if (count($statLineData) === 2 && trim($statLineData[0]) === "MemTotal") {
                            $memoryTotal = trim($statLineData[1]);
                            $memoryTotal = explode(" ", $memoryTotal);
                            $memoryTotal = $memoryTotal[0];
                            $memoryTotal *= 1024;  // convert from kibibytes to bytes
                        }

                        // Free memory
                        if (count($statLineData) === 2 && trim($statLineData[0]) === "MemFree") {
                            $memoryFree = trim($statLineData[1]);
                            $memoryFree = explode(" ", $memoryFree);
                            $memoryFree = $memoryFree[0];
                            $memoryFree *= 1024;  // convert from kibibytes to bytes
                        }
                    }
                }
            }
        }

        if (is_null($memoryTotal) || is_null($memoryFree)) {
            return '';
        } else {
//                return (100 - ($memoryFree * 100 / $memoryTotal));
            return "total : " . QuizzesInitFacade::getUsedMemory($memoryTotal) . ", free : " . QuizzesInitFacade::getUsedMemory($memoryFree). '. In percents : ' . (100 - ($memoryFree * 100 / $memoryTotal));
        }
    }

    public static function getNiceFileSize($bytes, $binaryPrefix=true) {
        if ($binaryPrefix) {
            $unit=array('B','KiB','MiB','GiB','TiB','PiB');
            if ($bytes==0) return '0 ' . $unit[0];
            return @round($bytes/pow(1024,($i=floor(log($bytes,1024)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
        } else {
            $unit=array('B','KB','MB','GB','TB','PB');
            if ($bytes==0) return '0 ' . $unit[0];
            return @round($bytes/pow(1000,($i=floor(log($bytes,1000)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
        }
    }


public static function isValidTimeStamp($timestamp)
    {
        if (empty($timestamp)) {
            return false;
        }
        if (gettype($timestamp) === 'object') {
            $timestamp = $timestamp->toDateTimeString();
        }

        return ((string)(int)$timestamp === (string)$timestamp)
               && ($timestamp <= PHP_INT_MAX)
               && ($timestamp >= ~PHP_INT_MAX);
    }

    public static function getFormattedDate($date, $date_format = 'mysql', $output_format = ''): string
    {
        if (empty($date)) {
            return '';
        }
        $date_carbon_format = config('app.date_carbon_format');
        if ($date_format === 'mysql' /*and ! isValidTimeStamp($date)*/) {
            $date_format = self::getDateFormat(\sergeynilov\QuizzesInit\Enums\DatetimeOutputFormat::dofAsText);
            $date        = Carbon::createFromTimestamp(strtotime($date))->format($date_format);

            return $date;
        }


        if (self::isValidTimeStamp($date)) {
            if (strtolower($output_format) === \sergeynilov\QuizzesInit\Enums\DatetimeOutputFormat::dofAsText) {
                $date_carbon_format_as_text = config('app.date_carbon_format_as_text', '%d %B, %Y');

                return Carbon::createFromTimestamp(
                    $date,
                   \Config::get('app.timezone')
                )->formatLocalized($date_carbon_format_as_text);
            }
            if (strtolower($output_format) === 'pickdate') {
                $date_carbon_format_as_pickdate = config('app.pickdate_format_submit');

                return Carbon::createFromTimestamp(
                    $date,
                   \Config::get('app.timezone')
                )->format($date_carbon_format_as_pickdate);
            }

            return Carbon::createFromTimestamp(
                $date,
               \Config::get('app.timezone')
            )->format($date_carbon_format);
        }
        $A = preg_split("/ /", $date);
        if (count($A) === 2) {
            $date = $A[0];
        }
        $a = Carbon::createFromFormat($date_carbon_format, $date);
        $b = $a->format(self::getDateFormat(\sergeynilov\QuizzesInit\Enums\DatetimeOutputFormat::dofAsText));

        return $a->format(self::getDateFormat(\sergeynilov\QuizzesInit\Enums\DatetimeOutputFormat::dofAsText));
    }

    public static function getDateFormat($format = ''): string
    {
        if (strtolower($format) === \sergeynilov\QuizzesInit\Enums\DatetimeOutputFormat::dofAsNumbers) {
            return self::$dateNumbersFormat;
        }
        if (strtolower($format) === \sergeynilov\QuizzesInit\Enums\DatetimeOutputFormat::dofAsText) {
            return self::$dateAstextFormat;
        }

        return self::$dateNumbersFormat;
    }


    public static function timeSpentLabel($startTime, $endTime, $replaceLabel = '')
    {
        $startTime = Carbon::parse($startTime);
        $endTime   = Carbon::parse($endTime);
        $timeleft = $startTime->diffForHumans($endTime);

        return str_replace($replaceLabel, '', $timeleft);
    }

    public static function getOSInfo($useBr = false)
    {
        $brCode  = ($useBr ? "<br>" : '') . PHP_EOL;
        $browser = new Parser(null, null, [
            'cache' => [
                'interval' => 86400 // This will override the default configuration.
            ]
        ]);

        $retText = $brCode . ' uname: ' . php_uname() . ', ' . $brCode . ' php version: ' . phpversion() . ', ' . $brCode . ' App version: ' . app()::VERSION . ', ';

        if ($browser->detect()) {
            $browserDetected = $browser->detect();
        }

        if ($browserDetected->browserName()) {
            $retText .= '' . $browserDetected->browserName() . ', ';
        }
        if ($browserDetected->platformName()) {
            $retText .= '' . $browserDetected->platformName() . ', ';
        }
        if ($browserDetected->deviceFamily()) {
            $retText .= '' . $browserDetected->deviceFamily() . ', ';
        }
        if ($browserDetected->isMobile()) {
            $retText .= 'Mobile, ';
        }
        if ($browserDetected->isDesktop()) {
            $retText .= 'Desktop, ';
        }
        if ($browserDetected->isTablet()) {
            $retText .= 'Tablet, ';
        }
        if ($browserDetected->isBot()) {
            $retText .= 'Bot, ';
        }
        if ($browserDetected->isLinux()) {
            $retText .= 'Linux, ';
        }
        $apacheGetModules = apache_get_modules();
        if (is_array($apacheGetModules) and count($apacheGetModules) > 0) {
            [$keys, $modules] = Arr::divide($apacheGetModules);
            $retText .= $brCode . 'apache modules: ' . Arr::join($modules, ', ');
        }

        $errLvl         = error_reporting();
        $errorReporting = '';
        for ($i = 0; $i < 15; $i++) {
            $errorReporting .= QuizzesInitFacade::FriendlyErrorType($errLvl & pow(2, $i)) . ', ';
        }

        $usedMemory = 'Used memory : ' . QuizzesInitFacade::getUsedMemory(true);
        $serverMemoryUsage = 'Server memory usage : '. QuizzesInitFacade::getServerMemoryUsage();
        return $retText . ', ' . $brCode . '  error reporting: ' . $errorReporting . ',  ' . $usedMemory . ',  ' . $serverMemoryUsage;
    }

    public static function FriendlyErrorType($type)
    {
        switch ($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }

        return "";
    } // FriendlyErrorType


    public static function varDump($var, $descr = '', bool $returnString = true): string
    {
//        return;
//        \Log::info( '00 varDump $var ::' . print_r( $var, true  ) );
//        \Log::info( '000 varDump gettype($var) ::' . print_r( gettype($var), true  ) );

        if (is_null($var)) {
            $outputStr = 'NULL :' . (! empty($descr) ? $descr . ' : ' : '') . 'NULL';
            if ($returnString) {
                return $outputStr;
            }
            \Log::info($outputStr);

            return '';
        }
        if (is_scalar($var)) {
            $outputStr = 'scalar => (' . gettype($var) . ') :' . (! empty($descr) ? $descr . ' : ' : '') . $var;
            if ($returnString) {
                return $outputStr;
            }
            \Log::info($outputStr);

            return '';
        }
//        \Log::info( -1);
        if (is_array($var)) {
//            \Log::info( -2);
            $outputStr = '[]';
            if (isset($var[0])) {
//                \Log::info( -22);
                if (is_subclass_of($var[0], 'Illuminate\Database\Eloquent\Model')) {
//                    \Log::info( -23);
                    $collectionClassBasename = is_string($var[0]) ? class_basename($var[0]) : '';
                    $outputStr               = ' Array(' . count(collect($var)->toArray()) . ' of ' . $collectionClassBasename . ') :' . (! empty($descr) ? $descr . ' : ' : '') . print_r(
                            collect($var)->toArray(),
                            true
                        );
                } else {
//                    \Log::info( -24);
                    $outputStr = 'Array(' . count($var) . ') :' . (! empty($descr) ? $descr . ' : ' : '') . print_r(
                            $var,
                            true
                        );
                }
            } else {
//                \Log::info( -41);
                $outputStr = 'Array(' . count($var) . ') :' . (! empty($descr) ? $descr . ' : ' : '') . print_r(
                        $var,
                        true
                    );
            }

//            \Log::info( -3);
            if ($returnString) {
                return $outputStr;
            }

//            \Log::info($outputStr );
            return '';
        }

//        \Log::info( -4);
//        \Log::info( '-0 varDump class_basename($var) ::' . print_r( class_basename($var), true  ) );
        if (class_basename($var) === 'Request' or class_basename($var) === 'LoginRequest') {
            $request     = request();
            $requestData = $request->all();
            $outputStr   = 'Request:' . (! empty($descr) ? $descr . ' : ' : '') . print_r(
                    $requestData,
                    true
                );
            if ($returnString) {
                return $outputStr;
            }
            \Log::info($outputStr);

            return '';
        }

        if (class_basename($var) === 'LengthAwarePaginator' or class_basename($var) === 'Collection') {
            $collectionClassBasename = '';
            if (isset($var[0])) {
                $collectionClassBasename = is_string($var[0]) ? class_basename($var[0]) : '';
            }
            $outputStr = ' Collection(' . count($var->toArray()) . ' of ' . $collectionClassBasename . ') :' . (! empty($descr) ? $descr . ' : ' : '') . print_r(
                    $var->toArray(),
                    true
                );
            if ($returnString) {
                return $outputStr;
            }
            \Log::info($outputStr);

            return '';
        }

        /*        if (!is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
                }*/
        if (gettype($var) === 'object') {
            if (is_subclass_of($var, 'Illuminate\Database\Eloquent\Model')) {
//            if ( get_parent_class($var) === 'Illuminate\Database\Eloquent\Model' ) {
                $outputStr = ' (Model Object of ' . get_class($var) . ') :' . (! empty($descr) ? $descr . ' : ' : '') . print_r($var/*->getAttributes()*/
                    ->toArray(),
                        true
                    );
                if ($returnString) {
                    return $outputStr;
                }
                \Log::info($outputStr);

                return '';
            }
            $outputStr = ' (Object of ' . get_class($var) . ') :' . (! empty($descr) ? $descr . ' : ' : '') . print_r(
                    (array)$var,
//            $outputStr = ' (Object of ' . get_class($var) . ') :' . (!empty($descr) ? $descr . ' : ' : '') . print_r((array)json_encode($var),
                    true
                );
            if ($returnString) {
                return $outputStr;
            }
            \Log::info($outputStr);

            return '';
        }
        //        \Log::info( '-2 varDump $var ::' . print_r( $var, true  ) );
        //        \Log::info( '-3 varDump gettype($var) ::' . print_r( gettype($var), true  ) );

    } // if ( ! function_exists('varDump')) {


}
