# Securing your web applications using the Symfony Security component

The code from [the conference of AFUP](https://www.meetup.com/fr-FR/afup-paris-php/events/253944518/)
that was held 28/08/2018 at JoliCode, Paris.

The slides are available [on my slideshare](https://fr.slideshare.net/VladyslavRiabchenko/scurisation-de-vos-applications-web-laide-du-composant-security-de-symfony).

| :arrow_left: [Task 5] | Task 6 | [Task 7] :arrow_right: |
| --- | --- | --- |

*Set up [HTTP authentication], authorize only authenticated users.*

`MainSecurityListener` resembles basic HTTP authentication. 
In this step `MainSecurityListener` is replaced with `Symfony\Component\Security\Http\Firewall\BasicAuthenticationListener` that implements basic HTTP authentication.
Then `AnonymousAuthenticationListener` is replaced with `AccessListener` in order to enforce authentication,
in other words to prevent requests with no credentials to pass.

Security is enforced by throwing an authentication exception:

- in `BasicAuthenticationListener` when credentials are present but not valid,
- in `AccessListener` when no token is present in token storage.

In these cases an exception is intercepted to call an authentication entry point.
It returns a new 401 response to invite user to enter/reenter his/her credentials.

Resume of changes:

- Custom `MainSecurityListener` is deleted.
- In [index.php] `$mainSecurityListener` is now of type `BasicAuthenticationListener`.
- In [index.php] `$anonListener` is replaced with `$accessListener` what throws an authentication exception when token is not present in token storage. 
This listener can also verify the roles of authenticated user (like `ROLE_USER`).
- in [index.php] an `$exceptionListener` of type `Symfony\Component\Security\Http\Firewall\ExceptionListener`
is added to listen to `KernelEvents::EXCEPTION` event. It will start an authentication process by returning 401 HTTP response.
- in [index.php] `$basicAuthenticationEntryPoint` of type `Symfony\Component\Security\Http\EntryPoint\BasicAuthenticationEntryPoint`
is added to start/restart an authentication. 

Urls to test:

* `/main` and enter "gordon" and "freeman" (authenticated as "gordon")
* `/main` and enter something else then "gordon" and "freeman" (authentication dialog reappears)
* `/secondary` (not authenticated)

Urls to test without rewrite rules must start with `/index.php`, e.g. `/index.php/main`.

[index.php]: public/index.php
[HTTP authentication]: https://en.wikipedia.org/wiki/Basic_access_authentication
[Task 5]: https://github.com/vria/symfony-security-component-use/tree/5-authentication-provider
[Task 7]: https://github.com/vria/symfony-security-component-use/tree/7-login-form
