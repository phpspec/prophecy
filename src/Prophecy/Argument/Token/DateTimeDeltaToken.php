<?php

namespace Prophecy\Argument\Token;


class DateTimeDeltaToken implements TokenInterface
{
    /** @var \DateTime */
    private $value;
    /** @var integer */
    private $delta;

    public function __construct(\DateTimeInterface $value, $delta)
    {
        $this->value = $value;
        $this->delta = $delta;
    }

    /**
     * Calculates token match score for provided argument.
     *
     * @param $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        if (!$argument instanceof \DateTimeInterface) {
            return false;
        }

        return abs($argument->getTimestamp() - $this->value->getTimestamp()) < $this->delta;
    }

    /**
     * Returns true if this token prevents check of other tokens (is last one).
     *
     * @return bool|int
     */
    public function isLast()
    {
        return false;
    }

    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('Date{%s}~%d', $this->value->format('Y-m-d H:i:s'), $this->delta);
    }
}