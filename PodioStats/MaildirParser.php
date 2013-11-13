<?php
namespace PodioStats;

class MaildirParser
{

    private $notReadable;
    private $data;
    
    public function __construct()
    {
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'vps.kaustik.com') {
            $filePathDir = '/home/zippo/Maildir/.Podio/cur/';
        } else {
            $filePathDir = 'tmp/.Podio/cur/';
        }
        $dir = scandir($filePathDir);
        $show = array();
        
        $this->data = new \ArrayObject();
        
        $this->notReadable = array();
        foreach ($dir as $file) {
            if (! is_readable($filePathDir . $file)) {
                $this->notReadable[] = $file;
                continue;
            }
            $arr = file($filePathDir . $file);
            if (empty($arr)) {
                continue;
            }
            $dateFound = false;
            $isMention = false;
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
            }
            if (! $dateFound) {
                echo "no date found in $file <br />";
            } else {
                $day->addNotice();
                if ($isMention) {
                    $day->addMention();
                }
            }
        }
        
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

}

