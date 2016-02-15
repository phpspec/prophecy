<?php
    /**
     * Created by PhpStorm.
     * User: jeff
     * Date: 25/08/2015
     * Time: 19:14
     */

    namespace Prophecy\Exception\Doubler;

    class MethodNotExtendableException extends DoubleException
    {
        private $methodName;

        private $className;

        /**
         * @param string $message
         * @param string $className
         * @param string $methodName
         */
        public function __construct($message, $className, $methodName)
        {
            parent::__construct($message);

            $this->methodName = $methodName;
            $this->className = $className;
        }


        /**
         * @return string
         */
        public function getMethodName()
        {
            return $this->methodName;
        }

        /**
         * @return string
         */
        public function getClassName()
        {
            return $this->className;
        }

    }
