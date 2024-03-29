<?php
namespace Vojtys\Forms\DatePicker;

use Nette;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils;

/**
 * Class DatePickerBase
 * @package Vojtys\Forms\DatePicker
 */
class DatePickerBase extends BaseControl
{
    const W3C_DATE_FORMAT = 'yyyy-mm-dd';
    const VOJTY_DATE_FORMAT = 'dd/mm/yyyy';

    const FIELD_NAME_START = 'start';
    const FIELD_NAME_END = 'end';
    const DEFAULT_SEPARATOR = ',';

    const DATE_TIME_MIN = 'Vojtys\Forms\Datepicker\DatepickerBase::validateDateTimeMin';
    const DATE_TIME_MAX = 'Vojtys\Forms\Datepicker\DatepickerBase::validateDateTimeMax';
    const DISABLED_DATES = 'Vojtys\Forms\Datepicker\DatepickerBase::validateDisabledDates';
    const DISABLED_DAYS = 'Vojtys\Forms\Datepicker\DatepickerBase::validateDisabledDays';

    const START_VIEW_MONTH = 0;
    const START_VIEW_YEAR = 1;
    const START_VIEW_DECADE = 2;

    const MIN_VIEW_MODE_DAYS = 0;
    const MIN_VIEW_MODE_MONTHS = 1;
    const MIN_VIEW_MODE_YEARS = 2;

    /** @var string */
    protected $language;

    /** @var Utils\DateTime */
    protected $startDateTime;

    /** @var Utils\DateTime */
    protected $endDateTime;

    /** @var int */
    protected $startView = self::START_VIEW_MONTH;

    /** @var int */
    protected $minViewMode = self::MIN_VIEW_MODE_DAYS;

    /** @var string */
    protected $dateTimeFormat = self::VOJTY_DATE_FORMAT;

    /** @var bool */
    protected $autoClose = false;

    /** @var bool */
    protected $todayHighlight = false;

    /** @var array */
    protected $daysOfWeekDisabled = [];

    /** @var array */
    protected $datesDisabled = [];

    /** @var mixed unfiltered submitted value */
    protected $rawValue = '';

    /** @var boolean */
    protected $time = true;

    /** @var string */
    protected $ico = 'glyphicon glyphicon-calendar';

    /** @var bool */
    protected $inline = false;

    /** @var bool */
    protected $multidate = false;

    /** @var string */
    protected $multidateSeparator = self::DEFAULT_SEPARATOR;

    /**
     * @param null $label
     * @param $config
     */
    public function __construct($label, $config)
    {
        parent::__construct($label, $config);
        $this->control->type = 'text';
    }

    protected function attached(IComponent $form): void
    {
        parent::attached($form);

        if (($form instanceof Form) && ($form->getTranslator())) {
            $locale = $form->getTranslator()->getLocale();
            $this->setLanguage($locale);
        } else {
            $this->setLanguage('cs');
        }
    }

    /**
     * @param $value
     * @return array|FALSE|Utils\DateTime|null
     * @throws DatePickerException
     */
    public function prepareValue($value)
    {
        if ($this->multidate) {
            $values = explode($this->multidateSeparator, $value);
            $value = [];
            foreach ($values as $item) {
                $value[] = $this->_prepareValue($item);
            }
        } else {
            $value = $this->_prepareValue($value);
        }

        return $value;
    }

    /**
     * @param $value
     * @return FALSE|Utils\DateTime|null
     * @throws DatePickerException
     */
    private function _prepareValue($value)
    {
        if ($value instanceof Utils\DateTime) {
            //...
        } elseif ($value instanceof \DateTime) {
            $value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value->format($this->getDateTimeFormat(TRUE)));
        } elseif (is_string($value) && ($value != '')) {
            $value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value);
        } elseif (empty($value)) {
            $value = NULL;
        }
        if ($value === FALSE) {
            throw new DatePickerException('Not valid string!');
        }
        if (($value instanceof Utils\DateTime) && ($this->time == FALSE)) {
            $value->setTime(0, 0, 0);
        }

        return $value;
    }

    /**
     * Converts javascript datetime format to php date format
     *
     * @param $str
     * @return string
     */
    protected function toPhpDateTimeFormat($str)
    {
        $f = str_replace(
            array('dd',   'd',    'mm',   'm',    'MM',   'M',    'yyyy', 'yyy',  'yy'),
            array('d',    'j',    'm',    'n',    'F',    'M',    'Y',    'y',    'y'),
            $str
        );
        return $f;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isInRange($value)
    {
        return (is_int($value) && (($value >= 0) && ($value <= 6)));
    }

    /**
     * Get settings for control
     *
     * @return array
     */
    protected function getControlSettings()
    {
        $settings = [
            'format' => $this->getDateTimeFormat(),
            'daysOfWeekDisabled' => $this->daysOfWeekDisabled,
            'autoclose' => $this->autoClose,
            'startView' => $this->startView,
            'minViewMode' => $this->minViewMode,
            'todayHighlight' => $this->todayHighlight,
            'inline' => $this->inline,
            'multidate' => $this->multidate,
            'multidateSeparator' => $this->multidateSeparator
        ];

        if (!empty($this->datesDisabled)) {
            $dates = [];
            foreach ($this->datesDisabled as $dateTime) {
                $dates[] = $dateTime->format($this->getDateTimeFormat(TRUE));
            }
            $settings['datesDisabled'] = $dates;
        }

        if ($this->startDateTime != NULL) {
            $settings['startDate'] = $this->startDateTime->format($this->getDateTimeFormat(TRUE));
        }

        if ($this->endDateTime != NULL) {
            $settings['endDate'] = $this->endDateTime->format($this->getDateTimeFormat(TRUE));
        }

        return $settings;
    }


    /**
     * @param $validator
     * @param null $message
     * @param array|null $arg
     *
     * @return $this|BaseControl
     *
     * @throws DatePickerException
     */
    public function addRule($validator, $message = NULL, $arg = NULL)
    {
        // check for disabled dates
        if ($validator == self::DISABLED_DATES) {
            if (!is_array($arg)) {
                throw new DatePickerException('Rule argument is not valid! Array of \DateTime objects is expected. '. get_class($arg) . ' given.');
            }
            foreach ($arg as $date) {
                if ($date instanceof \DateTime) {
                    // add only instances of DateTime
                    $this->setDisabledDate($date);
                }
            }
            parent::addRule($validator, $message, $this->datesDisabled);
        }

        // check for disabled days in week
        elseif ($validator == self::DISABLED_DAYS) {
            if (is_array($arg)) {

                // filter array values
                $arg = array_filter($arg, 'Vojtys\Forms\DatePicker\DatePickerBase::isInRange');
                $this->setDaysOfWeekDisabled($arg);
                parent::addRule($validator, $message, $arg);
            } else {
                throw new DatePickerException('Rule argument is not valid! Array is expected ' . get_class($arg) . ' given.');
            }
        }

        // check for minimal datetime value
        elseif ($validator == self::DATE_TIME_MIN) {
            if ($arg instanceof \DateTime) {
                $this->setStartDate($arg); // set datepicker option
                parent::addRule($validator, $message, $arg);
            } else {
                throw new DatePickerException('Rule argument is not valid! \DateTime object is expected. '. get_class($arg) . ' given.');
            }
        }

        // check for maximal datetime value
        elseif ($validator == self::DATE_TIME_MAX) {
            if ($arg instanceof \DateTime) {
                $this->setEndDate($arg); // set datepicker option
                parent::addRule($validator, $message, $arg);
            } else {
                throw new DatePickerException('Rule argument is not valid! \DateTime object is expected. '. get_class($arg) . ' given.');
            }
        }

        // default
        else {
            parent::addRule($validator, $message, $arg);
        }

        return $this;
    }

    /**
     * @param Nette\Forms\IControl $control
     * @param $dates
     *
     * @return bool
     *
     * @throws DatePickerException
     */
    public static function validateDisabledDates(Nette\Forms\IControl $control, $dates)
    {
        if (!$control instanceof self) {
            throw new DatePickerException('Unable to validate ' . get_class($control) . ' instance.');
        }

        // get control value(s)
        $values = clone $control->getValue();

        // value is needed to be an array
        $values = (!is_array($values)) ? [$values] : $values;

        // find disabled dates
        foreach ($values as $value) {
            foreach ($dates as $date) {
                if ($value != NULL) {

                    @$value->modify('midnight'); //  >PHP 5.3.6
                    if ($value == $date) {
                        return FALSE;
                    }
                }
            }
        }

        return TRUE;
    }

    /**
     * @param Nette\Forms\IControl $control
     * @param $dates
     *
     * @return bool
     *
     * @throws DatePickerException
     */
    public static function validateDisabledDays(Nette\Forms\IControl $control, $dates)
    {
        if (!$control instanceof self) {
            throw new DatePickerException('Unable to validate ' . get_class($control) . ' instance.');
        }

        // get control value(s)
        $values = $control->getValue();

        // value is needed to be an array
        $values = (!is_array($values)) ? [$values] : $values;

        // array of disabled days
        $settings = $control->getControlSettings();
        $disabled = isset($settings['daysOfWeekDisabled']) ? $settings['daysOfWeekDisabled'] : array();

        foreach ($values as $value) {
            if (($value !== NULL) && ($value instanceof Utils\Datetime)) {
                if (in_array($value->format('w'), $disabled)) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    /**
     * @param Nette\Forms\IControl $control
     * @param \DateTime $minDate
     *
     * @return bool
     *
     * @throws DatePickerException
     */
    public static function validateDateTimeMin(Nette\Forms\IControl $control, \DateTime $minDate = NULL)
    {
        if (!$control instanceof self) {
            throw new DatePickerException('Unable to validate ' . get_class($control) . ' instance.');
        }

        $value = $control->getValue();
        if (is_array($value)) {
            $value = (isset($value[ self::FIELD_NAME_START ])) ? $value[ self::FIELD_NAME_START ] : NULL;
        }

        return ($minDate === NULL || $value >= $minDate || $value === NULL);
    }

    /**
     * @param Nette\Forms\IControl $control
     * @param \DateTime $maxDate
     *
     * @return bool
     *
     * @throws DatePickerException
     */
    public static function validateDateTimeMax(Nette\Forms\IControl $control, \DateTime $maxDate = NULL)
    {
        if (!$control instanceof self) {
            throw new DatePickerException('Unable to validate ' . get_class($control) . ' instance.');
        }

        $value = $control->getValue();
        if (is_array($value)) {
            $value = (isset($value[ self::FIELD_NAME_END ])) ? $value[ self::FIELD_NAME_END] : NULL;
        }

        return ($maxDate === NULL || $value <= $maxDate || $value === NULL);
    }

    /**
     * @param $opt
     * @return $this
     */
    public function setTodayHighlight($opt)
    {
        $this->todayHighlight = $opt;
        return $this;
    }

    /**
     * @param $bool
     * @return mixed
     */
    public function setAutoClose($bool)
    {
        $this->autoClose = $bool;
        return $bool;
    }

    /**
     * @param $opt
     * @return $this
     */
    public function setStartView($opt)
    {
        $this->startView = $opt;
        return $this;
    }

    /**
     * @param $opt
     * @return $this
     */
    public function setMinViewMode($opt)
    {
        $this->minViewMode = $opt;
        return $this;
    }

    /**
     * @param bool $php
     * @return mixed|string
     */
    public function getDateTimeFormat($php = FALSE)
    {
        return ($php) ? $this->toPhpDateTimeFormat($this->dateTimeFormat) : $this->dateTimeFormat;
    }

    /**
     * @param $pattern
     */
    public function setDateTimeFormat($pattern)
    {
        $this->dateTimeFormat = $pattern;
    }

    /**
     * @param \DateTime $dateTime
     */
    protected function setStartDate(\DateTime $dateTime)
    {
        $this->startDateTime = $dateTime;
    }

    /**
     * @param \DateTime $dateTime
     */
    protected function setEndDate(\DateTime $dateTime)
    {
        $this->endDateTime = $dateTime;
    }

    /**
     * @param $days
     * @return $this
     */
    protected function setDisabledDate($date)
    {
        $this->datesDisabled[] = $date;
        return $this;
    }

    /**
     * @param $opt
     * @return $this
     */
    public function setDaysOfWeekDisabled($opt)
    {
        $this->getControlPrototype()->data('disabled-days', $opt);
        $this->daysOfWeekDisabled = $opt;
        return $this;
    }

    /**
     * @param $ico
     * @return $this
     */
    public function setIco($ico)
    {
        $this->ico = $ico;
        return $this;
    }

    /**
     * @return string
     */
    public function getIco()
    {
        return $this->ico;
    }

    /**
     * @return $this
     */
    public function disableTime()
    {
        $this->time = FALSE;
        return $this;
    }

    /**
     * @param string $separator
     * @return $this
     */
    public function allowMultidate($separator = self::DEFAULT_SEPARATOR)
    {
        if ($this instanceof DatePickerRange) {
            throw new DatePickerException('Multidate is not allowed for "datePickerRange".');
        }
        $this->multidate = TRUE;
        $this->multidateSeparator = $separator;
        return $this;
    }

    /**
     * @param string|null $lang
     */
    public function setLanguage($lang = null)
    {
        $this->language = $lang;
    }

    /**
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->language;
    }    
}