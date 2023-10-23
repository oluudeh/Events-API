<?php

namespace App\Entities;

use DateTime;
use App\Entities\BaseEntity;


class Event extends BaseEntity
{

    protected string $name;

    protected City $city;

    protected Country $country;

    protected DateTime $startDate;

    protected DateTime $endDate;

    public function getName(): string
    {
        return $this->name;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setCity(City $city)
    {
        $this->city = $city;
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    public function toArray(): array
    {
        $ret = [];

        if ($this->id) {
            $ret["id"] = $this->id;
        }

        if ($this->name) {
            $ret["name"] = $this->name;
        }

        if ($this->city) {
            $ret["city"] = $this->city->getName();
        }

        if ($this->country) {
            $ret["country"] = $this->country->getName();
        }

        if ($this->startDate) {
            $ret["startDate"] = $this->startDate->format("Y-m-d");
        }

        if ($this->endDate) {
            $ret["endDate"] = $this->endDate->format("Y-m-d");
        }

        return $ret;
    }
}
