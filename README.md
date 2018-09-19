# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| | Task 1 | [Task 2] :arrow_right: |
| --- | --- | --- |

*Authenticate each request to the application using an identifier and a password.*

To facilitate the task we expose a "front controller" to the user. 
This is a single PHP file through which all requests are processed. 

An [index.php] as a front controller will call [SecurityListener] at every request.
The purpose of [SecurityListener] as to authenticate a request, in particular :

- extract credentials from the Request object (query parameters "auth_user" and "auth_pw")
- verify credentials
- create Token if credentials are valid
- pass Token into TokenStorage. The last is a service accessible by any other code, 
e.g. [index.php].  

Urls to test:

* `/?auth_user=gordon&auth_pw=freeman` (authenticated)
* `/` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php?auth_user=gordon&auth_pw=freeman`.

[SecurityListener]: src/Security/SecurityListener.php
[index.php]: public/index.php
[Task 2]: https://github.com/vria/symfony-security-component-use/tree/2-firewall
