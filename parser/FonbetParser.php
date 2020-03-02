<?php
namespace Parser;

use Facebook\WebDriver\Exception\NoSuchElementException;
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
            $table_rows = $this->driver->findElements(WebDriverBy::className("table__row"));
            foreach($table_rows as $row) {
                try {
                    $title = $row->findElement(WebDriverBy::className("table__match-title-text"))->getText();
                    if ($title == $search_title) {
                        $elem_coeffs = $row->findElements(WebDriverBy::className("_type_btn"));
                        $coeffs = [];
                        foreach ($search_coeffs as $coeff) {
                            $coeffs[$coeff] = $elem_coeffs[$coeff - 1]->getText();
                            if (!$coeffs[$coeff]) {
                                $coeffs[$coeff] = "Пусто";
                            }
                        }
                        $result = new \stdClass();
                        $result->title = $title;
                        $result->coeffs = $coeffs;
                        break;
                    }
                } catch (NoSuchElementException $exc) {
                    continue;
                }
            }
        } catch (\Throwable $exc) {
            die(json_encode(['status' => 500, 'msg' => $exc->getMessage()]));
        }
        return $result;
    }

}
