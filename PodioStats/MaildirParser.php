<?php
namespace PodioStats;

class MaildirParser
{

    private $filePathDir;
    
    private $notReadable;
    private $data;
    
    public function __construct()
    {
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'vps.kaustik.com') {
            $this->filePathDir = '/home/zippo/Maildir/.Podio/cur/';
            $this->readFromMaildir();
        } else {
            $this->filePathDir = 'tmp/.Podio/cur/';
            //$this->readFromCache();
            $this->readFromMaildir();
            $this->filterOutWeekendFromData();
        }
        
    }
    public function readFromMaildir()
    {
        $dir = scandir($this->filePathDir);
        
        $this->data = new \ArrayObject();
        
        $this->notReadable = array();
        foreach ($dir as $file) {
            if (! is_readable($this->filePathDir . $file)) {
                $this->notReadable[] = $file;
                continue;
            }
            $arr = file($this->filePathDir . $file);
            if (empty($arr)) {
                continue;
            }
            $dateFound = false;
            $isMention = false;
            $isMessage = false;
            foreach ($arr as $row) {
                
                if (substr($row, 0, 5) == 'Date:') {
                    $dateFound = true;
                    $timeString = substr($row, 6);
                    $date = new \DateTime($timeString);
                    $ymd = $date->format('Ymd');
                    $day = Day::getDayFromArrayObjectForIndex($this->data, $ymd);
                    $day->setDate($date);
                    
                }
                
                if (substr($row, 0, 8) == 'Subject:' && stristr($row, 'mention')) {
                    $isMention = true;
                }

                if (substr($row, 0, 8) == 'Subject:' && stristr($row, 'message')) {
                    $isMessage = true;
                }
            }
            if (! $dateFound) {
                echo "no date found in $file <br />";
            } else {
                if ($isMessage) {
                    $day->addMessage();
                } else {
                    $day->addNotice();
                    if ($isMention) {
                        $day->addMention();
                    }
                }
            }
        }
    }
	
    public function readFromCache(){
        include('cache.php');
        $this->data = $object;
        $this->notReadable = array();
    }
	/**
     * @return the $notReadable
     */
    public function getNotReadable()
    {
        return $this->notReadable;
    }

	/**
     * @return the $data
     */
    public function getData()
    {
        return $this->data;
    }
    
    private function filterOutWeekendFromData(){

        /* @var $day Day */
        $newData = new \ArrayObject();
        foreach ($this->data as $key => $day) {
            $dayOfWeek = $day->getDate()->format('D');
            if ($dayOfWeek == 'Sat' && $dayOfWeek == 'Sun') {
                continue;
            }
            if($day->getNotices() > 100) {
                continue;
            }
            
            if($day->getNotices() < 30) {
                continue;
            }
            $newData->append($day);                
        }
        $this->data = $newData;
    }

}

