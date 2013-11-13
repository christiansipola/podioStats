<?php
namespace PodioStats;

    
class Day
{
    /**
     * 
     * @var int
     */
    private $notices;
    
    /**
     * 
     * @var int
     */
    private $mentions;

    /**
     * 
     * @var \DateTime
     */
    private $date;
    
    public function __construct()
    {
        $this->notices = 0;
        $this->mentions = 0;    
    }
    
	/**
     * @return int $notices
     */
    public function getNotices()
    {
        return $this->notices;
    }

	/**
     * @return int $mentions
     */
    public function getMentions()
    {
        return $this->mentions;
    }

	/**
     * @return \DateTime $date
     */
    public function getDate()
    {
        return $this->date;
    }

    public function addNotice()
    {
        $this->notices++;
    }

    public function addMention()
    {
        $this->mentions++;
    }

	/**
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getPercentMentions(){
        return round($this->mentions/$this->notices,3)*100;
    }
    
    /**
     * 
     * @param \ArrayObject $array
     * @param string $ymd
     * @return \PodioStats\Day
     */
    public static function getDayFromArrayObjectForIndex(\ArrayObject $array, $ymd){
        if (! $array->offsetExists($ymd)) {
            $day = new Day();
            $array->offsetSet($ymd, $day);
        } else {
            $day = $array->offsetGet($ymd);
        }
        return $day;
    }
    
    public static function getArrayAsLineChartJson(\ArrayObject $array)
    {
        /* @var $day Day */
        $data = array(
            'labels' => array(),
            'datasets' => array(
                array(
                    'fillColor' => 'rgba(220,220,220,0.5)',
                    'data'=>array()
                ),
                array(
                    'fillColor' => 'rgba(151,187,205,0.5)',
                    'data'=>array()
                )
            ),
        );        
        foreach ($array as $day) {
            $data['labels'][] = $day->getDate()->format('Y-m-d');
            $data['datasets'][0]['data'][] = $day->getNotices();
            $data['datasets'][1]['data'][] = $day->getMentions();
        }
        
        return json_encode($data);
    }
}
