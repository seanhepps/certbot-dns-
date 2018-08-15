<?php
/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2018-07-23 11:30:02
 * @version $Id$
 */
interface DnsAlterInterface
{
    public function addRecord($name, $value, $dnsInfo);
    public function deleteRecord($name, $value, $dnsInfo);
}