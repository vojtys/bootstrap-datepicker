<?php
namespace Vojtys\Forms\DatePicker;

use Nette\InvalidArgumentException;
use Nette\Utils;
use Nette\Forms\Form;

/**
 * Class DatePickerRange
 * @package Vojtys\Forms\DatePicker
 */
class DatePickerRange extends DatePickerBase
{

    function getControl()
    {
        // create input elements
        $elStart = parent::getControl();
        $elEnd = clone $elStart;

        // prepare input attributes
        $att = ['class' => 'form-control'];
        $name = $elStart->attrs['name'];

        $elStart->name($name . '[start]')
            ->id($elStart->attrs['id'] . '-start')
            ->addAttributes($att)
            ->value($this->getStartValue());
        $elEnd->name($name . '[end]')
            ->id($elStart->attrs['id'] . '-end')
            ->addAttributes($att)
            ->value($this->getEndValue());

        // group add
        $junction = ($translator = $this->getTranslator()) ? $translator->translate('do') : 'do';
        $groupAdd = Utils\Html::el('span')
            ->addAttributes(['class' => 'input-group-addon'])
            ->setText($junction);

        // generate field group
        $group = Utils\Html::el('div');
        $group->addAttributes([
            'data-vojtys-forms-datepicker' => '',
            'class' => 'input-daterange input-group',
            'data-locale' => $this->getLanguage(),
            'data-settings' => $this->getControlSettings(),
        ]);

        // add field and icon
        $group->addHtml($elStart);
        $group->addHtml($groupAdd);
        $group->addHtml($elEnd);

        return $group;
    }

    /**
     * @return null|string
     */
    public function getStartValue()
    {
        if (!isset($this->value[self::FIELD_NAME_START])) {
            return NULL;
        }

        $start = $this->value[self::FIELD_NAME_START];
        return ($start instanceof Utils\DateTime) ? $start->format($this->getDateTimeFormat(TRUE)) : NULL;
    }

    /**
     * @return null|string
     */
    public function getEndValue()
    {
        if (!isset($this->value[self::FIELD_NAME_END])) {
            return NULL;
        }

        $end = $this->value[self::FIELD_NAME_END];
        return ($end instanceof Utils\DateTime) ? $end->format($this->getDateTimeFormat(TRUE)) : NULL;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();
        return $value;
    }

    /**
     * @param $values
     * @return $this|\Nette\Forms\Controls\BaseControl
     *
     * @throws \Nette\InvalidArgumentException
     */
    public function setValue($values)
    {
        if (count($values) > 2) {
            throw new InvalidArgumentException();
        }

        if (!empty($values) && is_array($values)) {
            foreach ($values as $key => $value) {
                if ($value === NULL || $value === FALSE) {
                    unset($values[ $key ]);
                } else {
                    $values[ $key ] = $this->prepareValue($value);
                }
            }
            $values = $this->swap($values);
        }

        $this->value = empty($values) ? NULL : $values;
        return $this;
    }

    public function loadHttpData()
    {
        $this->setValue([
            self::FIELD_NAME_START => $this->getHttpData(Form::DATA_LINE, '[start]'),
            self::FIELD_NAME_END => $this->getHttpData(Form::DATA_LINE, '[end]'),
        ]);
    }

    /**
     * @param $values
     * @return mixed
     */
    public function swap($values)
    {
        if (count($values) < 2) {
            return $values;
        }

        if ($values[self::FIELD_NAME_START] > $values[self::FIELD_NAME_END]) {
            $pom = $values[self::FIELD_NAME_START];
            $values[self::FIELD_NAME_START] = $values[self::FIELD_NAME_END];
            $values[self::FIELD_NAME_END] = $pom;
        }

        unset($pom);
        return $values;
    }
}