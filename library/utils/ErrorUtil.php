<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

/**
 * ErrorUtil class file.
 * 
 * @package usni\library\utils
 */
class ErrorUtil
{

    /**
     * Converts arguments array to its string representation
     *
     * @param array $args arguments array to be converted
     * @return string string representation of the arguments array
     */
    public static function argumentsToString($args)
    {
        $count = 0;

        $isAssoc = $args !== array_values($args);

        foreach ($args as $key => $value)
        {
            $count++;
            if ($count >= 5)
            {
                if ($count > 5)
                    unset($args[$key]);
                else
                    $args[$key] = '...';
                continue;
            }

            if (is_object($value))
                $args[$key] = get_class($value);
            elseif (is_bool($value))
                $args[$key] = $value ? 'true' : 'false';
            elseif (is_string($value))
            {
                if (strlen($value) > 64)
                    $args[$key] = '"' . substr($value, 0, 64) . '..."';
                else
                    $args[$key] = '"' . $value . '"';
            }
            elseif (is_array($value))
                $args[$key] = 'array(' . static::argumentsToString($value) . ')';
            elseif ($value === null)
                $args[$key] = 'null';
            elseif (is_resource($value))
                $args[$key] = 'resource';

            if (is_string($key))
            {
                $args[$key] = '"' . $key . '" => ' . $args[$key];
            }
            elseif ($isAssoc)
            {
                $args[$key] = $key . ' => ' . $args[$key];
            }
        }
        $out = implode(", ", $args);

        return $out;
    }

    /**
     * Returns a value indicating whether the call stack is from application code.
     * @param array $trace the trace data
     * @return boolean whether the call stack is from application code.
     */
    public static function isCoreCode($trace)
    {
        if (isset($trace['file']))
        {
            $systemPath = realpath(Yii::getFrameworkPath());
            return $trace['file'] === 'unknown' || strpos(realpath($trace['file']), $systemPath . DIRECTORY_SEPARATOR) === 0;
        }
        return false;
    }

    /**
     * Renders the source code around the error line.
     * @param string $file source file path
     * @param integer $errorLine the error line number
     * @param integer $maxLines maximum number of lines to display
     * @return string the rendering result
     */
    public static function renderSourceCode($file, $errorLine, $maxLines)
    {
        $errorLine--; // adjust line number to 0-based from 1-based
        if ($errorLine < 0 || ($lines = @file($file)) === false || ($lineCount = count($lines)) <= $errorLine)
            return '';

        $halfLines = (int) ($maxLines / 2);
        $beginLine = $errorLine - $halfLines > 0 ? $errorLine - $halfLines : 0;
        $endLine = $errorLine + $halfLines < $lineCount ? $errorLine + $halfLines : $lineCount - 1;
        $lineNumberWidth = strlen($endLine + 1);

        $output = '';
        for ($i = $beginLine; $i <= $endLine;  ++$i)
        {
            $isErrorLine = $i === $errorLine;
            $code = sprintf("<span class=\"ln" . ($isErrorLine ? ' error-ln' : '') . "\">%0{$lineNumberWidth}d</span> %s", $i + 1, CHtml::encode(str_replace("\t", '    ', $lines[$i])));
            if (!$isErrorLine)
                $output.=$code;
            else
                $output.='<span class="error">' . $code . '</span>';
        }
        return '<div class="code"><pre>' . $output . '</pre></div>';
    }

    /**
     * Gets error info
     * @param \yii\web\HttpException|\Exception $exception
     * @param \yii\web\ErrorHandler $handler
     * @return array
     */
    public static function getInfo($exception, $handler)
    {
        if ($exception instanceof \yii\web\HttpException)
        {
            $code = $exception->statusCode;
        }
        else
        {
            $code = $exception->getCode();
        }
        $name = $handler->getExceptionName($exception);
        if ($name === null)
        {
            $name = 'Error';
        }
        if ($code)
        {
            $name .= " (#$code)";
        }

        if ($exception instanceof \yii\base\UserException)
        {
            $message = $exception->getMessage();
        }
        else
        {
            $message = 'An internal server error occurred.';
        }
        return ['code' => $code,
                'name' => $name,
                'message' => $message,
                'pageTitle' => static::resolvePageTitle($exception, $handler)];
    }

    /**
     * Resolve error page title
     * @param \yii\web\HttpException $exception
     * @param \yii\web\ErrorHandler $handler
     * @return string
     */
    public static function resolvePageTitle($exception, $handler)
    {
        $name = $handler->getExceptionName($exception);
        if ($exception instanceof \yii\web\HttpException)
        {
            return (int) $exception->statusCode . ' ' . $handler->htmlEncode($name);
        }
        else
        {
            $name = $handler->getExceptionName($exception);
            if ($name !== null)
            {
                return $handler->htmlEncode($name . ' – ' . get_class($exception));
            }
            else
            {
                return $handler->htmlEncode(get_class($exception));
            }
        }
    }
}