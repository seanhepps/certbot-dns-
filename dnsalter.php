<?php
/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2018-07-06 13:00:52
 * @version $Id$
 */
class DnsAlter
{
    // txt记录头
    public $txtName = '_acme-challenge';
    protected $domain = '';
    protected $value = '';
    protected $dns = null;
    protected $dnsInfo = null;

    public function __construct()
    {
        $this->domain = c('certbot_domain');
        $this->value = c('certbot_validation');

        $domain = str_replace('.', '_', $this->domain);

        $dnsName = c('domain.'. $domain . '.dns');
        require __DIR__ . '/dns/' . ucfirst($dnsName) . '.php';
        $dnsName = ucfirst($dnsName);
        $this->dns = new $dnsName;
        if (!$this->dns instanceof DnsAlterInterface) {
            Debug::log(c('domain.'. $domain . '.dns') . ' is not instance of DnsAlterInterface');
            exit;
        }
        $this->dnsInfo = array_merge((array)c('dns.'. $dnsName),(array)c('domain.'. $domain));
    }

    public function __call($method, $data)
    {
        $this->dns->$method($this->domain, $this->value, $this->dnsInfo);
    }
}