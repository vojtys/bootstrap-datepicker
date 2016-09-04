<?php
namespace Vojtys\Forms\DatePicker;

use Nette\Utils;
use Nette\Forms\Form;

/**
 * Class DatePickerInput
 * @package Vojtys\Forms\DatePicker
 */
class DatePickerInput extends DatePickerBase
{

    function getControl()
    {
        // create input element
        $el = parent::getControl();
        $el->addAttributes(array('class' => 'form-control'));

        // converts datetime value into php format
        // $TODO
        if ($this->multidate && is_array($this->value)) {
            $pom = [];
            foreach ($this->value as $v) {
                if ($v !== NULL) {
                    array_push($pom, $v->format($this->toPhpDateTimeFormat($this->dateTimeFormat)));
                }
            }
            $value = implode($this->multidateSeparator, $pom);
        } else {
            $value = $this->value ? $this->value->format($this->toPhpDateTimeFormat($this->dateTimeFormat)) : NULL;
        }
        $el->value = $value;

        // generate field group
        $group = Utils\Html::el('div')->add($el);

        if ($this->inline) {
            $el->addAttributes(array('style' => 'display:none;'));
            $group->add(Utils\Html::el('div'));
        } else {
            $group->add(Utils\Html::el('span')->class('input-group-addon')
                ->add(Utils\Html::el('span')->class($this->getIco()))
            );
        }

        $group->addAttributes([
            'data-vojtys-forms-datepicker' => '',
            'class' => 'input-group date',
            'data-locale' => $this->getLanguage(),
            'data-settings' => $this->getControlSettings()
        ]);

        return $group;
    }

    /**
     * @param $value
     * @return $this|\Nette\Forms\Controls\BaseControl
     *
     * @throws DatePickerException
     */
    public function setValue($value)
    {
        $this->value = $this->prepareValue($value);
        return $this;
    }

    /**
     * @return FALSE|mixed|Utils\DateTime|null
     */
    public function getValue()
    {
        $value = parent::getValue();
        if ($value instanceof Utils\DateTime) {
            return $value;
        } elseif (is_array($value)) {
            return $value;
        } else if ($value === FALSE || $value === NULL) {
            return NULL;
        } else {
            $value = Utils\DateTime::createFromFormat($this->getDateTimeFormat(TRUE), $value);
            if ($value === FALSE) {
                return NULL;
            }
        }
        return $value;
    }

    public function loadHttpData()
    {
        $this->setValue($this->getHttpData(Form::DATA_LINE));
    }

    /**
     * @return $this
     */
    public function setInlineTypeOn()
    {
        $this->inline = TRUE;
        return $this;
    }
}