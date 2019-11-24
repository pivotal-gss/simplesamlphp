SimpleSAMLphp
=============

This is NOT the official repository of the SimpleSAMLphp software.

* [Original Source Repo](https://github.com/simplesamlphp)
* [SimpleSAMLphp homepage](https://simplesamlphp.org)
* [SimpleSAMLphp Downloads](https://simplesamlphp.org/download)

This is a code fork for the simplesamlphp in it's configured, ready to deploy to Cloud Foundry format.

This is used for to test SAML authentication with Ops Manager, Bosh Director, PAS, and PKS

# Preconfigured Users
simplesamlphp Admin login: admin / password

IDP Admin User login: admin1 / password and admin2 / password [ mapped to 'opsmanadmin', 'pasadmin', 'pksadmin' groups [config/authsources.php](config/authsources.php) ]<br>
IDP User login: user1 / password, user2 / password [ mapped to 'opsmanuser', 'pasuser', 'pksuser' groups see [config/authsources.php](config/authsources.php)]


# Deployment 

* Edit the [metadata/saml20-sp-remote.php](metadata/saml20-sp-remote.php), replace `<OPSMAN_FQDN>, <BOSH_DIRECTOR_IP>, <SYSTEM_DOMAIN>`, <PKSAPI_FQDN> accordingly, comment out metadata section which will not be used. 

```
# Ops Manager
$metadata['https://<OPSMAN_FQDN>:443/uaa'] = [
    'AssertionConsumerService' => 'https://<OPSMAN_FQDN>:443/uaa/saml/SSO/alias/<OPSMAN_FQDN>',
    'SingleLogoutService' => 'https://<OPSMAN_FQDN>:443/uaa/saml/SingleLogout/alias/<OPSMAN_FQDN>',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'simplesaml.nameidattribute' => 'emailAddress',
];

# BOSH Director
$metadata['https://<BOSH_DIRECTOR_IP>:8443'] = [
    'AssertionConsumerService' => 'https://<BOSH_DIRECTOR_IP>:8443/saml/SSO/alias/<BOSH_DIRECTOR_IP>',
    'SingleLogoutService' => 'https://<BOSH_DIRECTOR_IP>:8443/saml/SingleLogout/alias/<BOSH_DIRECTOR_IP>',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'simplesaml.nameidattribute' => 'emailAddress',
];

# PAS 
$metadata['http://login.<SYSTEM_DOMAIN>'] = [
    'AssertionConsumerService' => 'https://login.<SYSTEM_DOMAIN>/saml/SSO/alias/login.<SYSTEM_DOMAIN>',
    'SingleLogoutService' => 'https://login.<SYSTEM_DOMAIN>/saml/SingleLogout/alias/login.<SYSTEM_DOMAIN>',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'simplesaml.nameidattribute' => 'emailAddress',
];

# PKS
$metadata['<PKSAPI_FQDN>:8443'] = [
    'AssertionConsumerService' => 'https://<PKSAPI_FQDN>:8443/saml/SSO/alias/<PKSAPI_FQDN>:8443',
    'SingleLogoutService' => 'https://<PKSAPI_FQDN>:8443/saml/SingleLogout/alias/<PKSAPI_FQDN>:8443',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'simplesaml.nameidattribute' => 'emailAddress',
];
```

Important NOTE: PAS tile hard codes the entityid with http instead of https and this causes some issues with simplesamlphp. This commit 7c467ce5e065651f4d57423dcd08f64fa883a721 works around the problem but the SP metadata need to have http to match the IDP metatdata with the UAA.

Important NOTE: By default PAS UAA SAML entity ID is `http://login.<SYSTEM_DOMAIN>`, but it can be overrided at "Ops Manager > PAS > UAA > SAML Entity ID Override". In above configuration, `$metadata[***]` must match the identity ID if it's configured. 

* `cf push` to any PCF

* The saml metadata endpoint would be something like https://simplesamlphp.<APPS_DOMAIN>/saml2/idp/metadata.php

# Configuration for Ops Manager and BOSH Director

*  Ops Manager > admin> Settings > SAML Settings > Change Authentication Method to SAML
  * SAML IDP Metadata: (XML downloaded from `https://simplesamlphp.<APPS_DOMAIN>/saml2/idp/metadata.php`)
  * BOSH IDP Metadata: left blank, by default use above
  * SAML Admin Group: 'opsmanadmin' (matches [config/authsources.php](config/authsources.php))
  * Groups Attribute: 'groups' (matches [config/authsources.php](config/authsources.php))

# Configuration for PAS UAA
* Ops Manager > PAS > Authentication and Enterprise SSO
  * Select "SAML Identity Provider"
  * Provider Name: simplesamlphp
  * Display Name: simplesamlphp
  * Provider Metadata: (XML downloaded from `https://simplesamlphp.<APPS_DOMAIN>/saml2/idp/metadata.php`)
  * (OR) Provider Metadata URL: `https://simplesamlphp.<APPS_DOMAIN>/saml2/idp/metadata.php`
  * External Groups Attribute: 'groups'
  * Other settings are optional.
* Map PAS UAA admin group to IDP group

```
uaac target uaa.<SYSTEM_DOMAIN> --skip-ssl-validation
uaac token client get admin -s ADMIN-CLIENT-SECRET
uaac group map --name scim.read "pasadmin" --origin simplesamlphp
uaac group map --name scim.write "pasadmin" --origin simplesamlphp
uaac group map --name cloud_controller.admin "pasadmin" --origin simplesamlphp
cf login --sso -a api.<SYSTEM_DOMAIN>
API endpoint: api.<SYSTEM_DOMAIN>

Temporary Authentication Code ( Get one at https://login.<SYSTEM_DOMAIN>/passcode )>
Authenticating...
OK
```

# Configuration for PKS UAA
* Ops Manager > PKS > UAA > SAML Identity Provider
  * Provider Name: simplesamlphp
  * Display Name: simplesamlphp
  * Provider Metadata: (XML downloaded from `https://simplesamlphp.<APPS_DOMAIN>/saml2/idp/metadata.php`)
  * (OR) Provider Metadata URL: `https://simplesamlphp.<APPS_DOMAIN>/saml2/idp/metadata.php`
  * External Groups Attribute: 'groups'
  * Other settings are optional.
* Map PAS UAA admin group to IDP group
	
```
uaac target <PKS_API>:8443 --skip-ssl-validation
uaac token client get admin -s ADMIN-CLIENT-SECRET
uaac group map --name pks.clusters.admin pksadmin --origin simplesamlphp
uaac group map --name pks.clusters.manage pksadmin --origin simplesamlphp

pks login -a <PKS_API> --sso-auto --skip-ssl-verification
You will now be taken to your browser for authentication
...
```