<?php

namespace DoctrineORMModule\Proxy\__CG__\Whathood\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class WhathoodUser extends \Whathood\Entity\WhathoodUser implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', 'id', 'ip_address', 'user_polygons');
        }

        return array('__isInitialized__', 'id', 'ip_address', 'user_polygons');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (WhathoodUser $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', array($id));

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getIpAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIpAddress', array());

        return parent::getIpAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setIpAddress($ip_address)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIpAddress', array($ip_address));

        return parent::setIpAddress($ip_address);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'toArray', array());

        return parent::toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, '__toString', array());

        return parent::__toString();
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($index)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'offsetExists', array($index));

        return parent::offsetExists($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($index)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'offsetGet', array($index));

        return parent::offsetGet($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($index, $newval)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'offsetSet', array($index, $newval));

        return parent::offsetSet($index, $newval);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($index)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'offsetUnset', array($index));

        return parent::offsetUnset($index);
    }

    /**
     * {@inheritDoc}
     */
    public function append($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'append', array($value));

        return parent::append($value);
    }

    /**
     * {@inheritDoc}
     */
    public function getArrayCopy()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getArrayCopy', array());

        return parent::getArrayCopy();
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'count', array());

        return parent::count();
    }

    /**
     * {@inheritDoc}
     */
    public function getFlags()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFlags', array());

        return parent::getFlags();
    }

    /**
     * {@inheritDoc}
     */
    public function setFlags($flags)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setFlags', array($flags));

        return parent::setFlags($flags);
    }

    /**
     * {@inheritDoc}
     */
    public function asort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'asort', array());

        return parent::asort();
    }

    /**
     * {@inheritDoc}
     */
    public function ksort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'ksort', array());

        return parent::ksort();
    }

    /**
     * {@inheritDoc}
     */
    public function uasort($cmp_function)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'uasort', array($cmp_function));

        return parent::uasort($cmp_function);
    }

    /**
     * {@inheritDoc}
     */
    public function uksort($cmp_function)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'uksort', array($cmp_function));

        return parent::uksort($cmp_function);
    }

    /**
     * {@inheritDoc}
     */
    public function natsort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'natsort', array());

        return parent::natsort();
    }

    /**
     * {@inheritDoc}
     */
    public function natcasesort()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'natcasesort', array());

        return parent::natcasesort();
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'unserialize', array($serialized));

        return parent::unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'serialize', array());

        return parent::serialize();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIterator', array());

        return parent::getIterator();
    }

    /**
     * {@inheritDoc}
     */
    public function exchangeArray($array)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'exchangeArray', array($array));

        return parent::exchangeArray($array);
    }

    /**
     * {@inheritDoc}
     */
    public function setIteratorClass($iteratorClass)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIteratorClass', array($iteratorClass));

        return parent::setIteratorClass($iteratorClass);
    }

    /**
     * {@inheritDoc}
     */
    public function getIteratorClass()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIteratorClass', array());

        return parent::getIteratorClass();
    }

}
