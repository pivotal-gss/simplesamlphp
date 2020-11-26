<?php

/**
 * SAML 2.0 remote SP metadata for SimpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-sp-remote
 */

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

# SSO for Apps 
$metadata['http://<SSO_FQDN>'] = [
    'AssertionConsumerService' => 'https://<SSO_FQDN>/saml/SSO/alias/<SSO_FQDN>',
    'SingleLogoutService' => 'https://<SSO_FQDN>/saml/SingleLogout/alias/<SSO_FQDN>',
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'simplesaml.nameidattribute' => 'emailAddress',
];
