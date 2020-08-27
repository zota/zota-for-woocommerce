<?php

namespace Zotapay;

/**
 * Class OrdersReportData.
 */
class OrdersReportData extends AbstractData
{
    /**
     * Request dateType
     *
     * @var string
     */
    protected $dateType;

    /**
     * Request endpointIds
     *
     * @var null|string
     */
    protected $endpointIds;

    /**
     * Request fromDate
     *
     * @var string
     */
    protected $fromDate;

    /**
     * Request statuses
     *
     * @var null|string
     */
    protected $statuses;

    /**
     * Request toDate
     *
     * @var string
     */
    protected $toDate;

    /**
     * Request types
     *
     * @var null|string
     */
    protected $types;

    /**
     * Get the value of Request dateType
     *
     * @return string
     */
    public function getDateType()
    {
        return $this->dateType;
    }

    /**
     * Set the value of Request dateType
     *
     * @param string $dateType
     *
     * @return self
     */
    public function setDateType($dateType)
    {
        $this->dateType = $dateType;

        return $this;
    }

    /**
     * Get the value of Request endpointIds
     *
     * @return null|string
     */
    public function getEndpointIds()
    {
        return $this->endpointIds;
    }

    /**
     * Set the value of Request endpointIds
     *
     * @param null|string $endpointIds
     *
     * @return self
     */
    public function setEndpointIds(?string $endpointIds)
    {
        $this->endpointIds = $endpointIds;

        return $this;
    }

    /**
     * Get the value of Request fromDate
     *
     * @return string
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set the value of Request fromDate
     *
     * @param string $fromDate
     *
     * @return self
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get the value of Request statuses
     *
     * @return null|string
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * Set the value of Request statuses
     *
     * @param null|string $statuses
     *
     * @return self
     */
    public function setStatuses(?string $statuses)
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * Get the value of Request toDate
     *
     * @return string
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set the value of Request toDate
     *
     * @param string $toDate
     *
     * @return self
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get the value of Request types
     *
     * @return null|string
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set the value of Request types
     *
     * @param null|string $types
     *
     * @return self
     */
    public function setTypes(?string $types)
    {
        $this->types = $types;

        return $this;
    }
}
