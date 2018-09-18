# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| :arrow_left: [Task 6] | Task 7 | |
| --- | --- | --- |

*Set up login form authentication for another part of the application.*

Basic http authentication added at previous step is active only when request's path starts with `/main`.
In this step we will add another authentication system that relies on login form and session 
for paths starting with `/front`. It will be possible to authenticate the same user for both
`/main` and `/front` parties of the application at the same time. 

Login form authentication is implemented by sequence of two listeners:

- An instance of `ContextListener` that retrieves previously authenticated token from the session during `REQUEST` event.
It also saves token to session during `RESPONSE` event.
- An instance of `UsernamePasswordFormAuthenticationListener` that listens 
for login form being send (`POST` request to `/front/login_check` path).
It extracts credentials, creates token, authenticates it and puts it to the token storage.

Resume of changes:

- [Controller] is extended with `loginFormAction` method to show the login form.
- [ControllerResolver] is extended with `$routes` attribute that maps path pattern to controller callable.
In there is no match the `$default` controller is returned. 
- In [index.php] the routes are configured show login form 
when request is made to `/front/login` path.
- In [index.php] session is created and passed to the request instance.
- In [index.php] `$contextListener` of type `Symfony\Component\Security\Http\Firewall\ContextListener` is created.
- In [index.php] `$formAuthListener` of type `Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener` is created.
It depends on bunch of other elements like authentication success handler to redirect user after successful login,
authentication failure handler to redirect user if login fails, some configuration to indicate
the path (`/front/login_check`) or request method (`POST`) to listen on, etc.
- In [index.php] `$contextListener`, `$formAuthListener` are added under `^/front` path in firewall map.

Urls to test:

* `/main` and enter "gordon" and "freeman" (authenticated as "gordon")
* `/main` and enter something else then "gordon" and "freeman" (authentication dialog reappears)
* `/front` (not authenticated)
* `GET` to `/front/login` shows login form (not authenticated)
*  submit `/front/login` with "gordon" as login and "freeman" as password (redirected to `/front/success`, authenticated as "gordon")
*  logged as "gordon" under `/front/success` go to `/front/other-page` (authenticated as "gordon")
* `/secondary` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php/main`.

[index.php]: public/index.php
[Controller]: src/Controller.php 
[ControllerResolver]: src/ControllerResolver.php
[HTTP authentication]: https://en.wikipedia.org/wiki/Basic_access_authentication
[Task 6]: https://github.com/vria/symfony-security-component-use/tree/6-http-basic
