# fair[&]smart - simple consent v2 integration example

Some code to demonstrate how to integrate a consent v2 form created using Fair and Smart Right Consent platform.

## in a nutshell
* put your own values (organization ID, model ID etc ...) in config.ini ;
* build the image : `docker build --tag simple-consentv2-integration-example .`
* run the container : `docker run simple-consentv2-integration-example`

## configuration
Configuration can either be done in config.ini or using environment variables (especially useful when running using
docker).

| parameter                  | environment variable name  | config file key | 
|----------------------------|---|-----------------|
| auth server url            | AUTH_URL | auth_url        |
| auth server realm          | AUTH_REALM | auth_realm      |
| auth server clientid       | AUTH_CLIENT_ID | auth_client_id  |
| auth server username       | AUTH_USERNAME | auth_username   |
| auth server password       | AUTH_PASSWORD | auth_password   |
| consent manager server url | CM_URL | cm_url          |
| consent manager API KEY | CM_KEY | cm_key          |

Configuration file is loaded from CONFIG_FILE_PATH (default : "config.ini"). 
 
## run example
Under docker, using a specific config file "my-config.ini" :

`docker run --mount type=bind,source=$PWD/my-config.ini,target=/var/www/html/my-config.ini -e CONFIG_FILE_PATH="my-config.ini" simple-consentv2-integration-example`
