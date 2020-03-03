<?php
namespace Parser;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\JavaScriptExecutor;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

/** @property string $host
 *  @property RemoteWebDriver $driver
 *  @property string $url
 */
class FonbetParser
{
    private $host;
    private $driver;
    private $url = "https://www.fonbet.ru/#!/live";

    // Selenium-server 4.x
    function __construct($server_host = 'http://localhost:4444/')
    {
        $this->host = $server_host;
    }

    function __destruct()
    {
        $this->driver->quit();
    }

    function createFirefoxRemoteDriver() {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
        try {
            $this->driver = RemoteWebDriver::create($this->host, $desiredCapabilities);
        } catch (\Throwable $exc) {
            die(json_encode(['status' => 500, 'msg' => "Couldn't create RemoteWebDriver instance"]));
        }
    }

    function searchMatch($search_title, $search_coeffs) {
        $result = null;
        try {
            $this->driver->get($this->url);
            $this->driver->wait(120)->
                            until(WebDriverExpectedCondition::visibilityOfAnyElementLocated(
                                WebDriverBy::className("table__row")));
            $file = file_get_contents(__DIR__."/parser.js");
            $result = $this->driver->executeScript($file, [$search_title, $search_coeffs]);
        } catch (\Throwable $exc) {
            die(json_encode(['status' => 500, 'msg' => $exc->getMessage()]));
        }
        return $result;
    }
}
